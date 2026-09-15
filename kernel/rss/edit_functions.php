<?php
/**
 * File containing the eZRSSEditFunction class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZRSSEditFunction
{
    /*!
     Store RSSExport

     \static
     \param Module
     \param HTTP
     \param publish ( true/false )
    */
    static function storeRSSExport( $Module, $http, $publish = false, $skipValuesID = null )
    {
        $valid = true;
        $validationErrors = array();

        /* Kill the RSS cache in all siteaccesses */
        $config = eZINI::instance( 'site.ini' );
        $cacheDir = eZSys::cacheDirectory();

        $availableSiteAccessList = $config->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        foreach ( $availableSiteAccessList as $siteAccess )
        {
            $cacheFilePath = $cacheDir . '/rss/' . md5( $siteAccess . $http->postVariable( 'Access_URL' ) ) . '.xml';
            $cacheFile = eZClusterFileHandler::instance( $cacheFilePath );
            if ( $cacheFile->exists() )
            {
                $cacheFile->delete();
            }
        }

        $db = eZDB::instance();
        $db->begin();

        // An OPML export lists feeds, not articles: it has no content source
        // and no class mapping, so the source rows below are neither read nor
        // validated for one. Validating them would fail an export that is
        // perfectly correct, because its empty source row names no class.
        $isOPML = $http->hasPostVariable( 'RSSVersion' )
                  && $http->postVariable( 'RSSVersion' ) === 'OPML';

        /* Create the new RSS feed */
        for ( $itemCount = 0; !$isOPML && $itemCount < $http->postVariable( 'Item_Count' ); $itemCount++ )
        {
            if ( $skipValuesID == $http->postVariable( 'Item_ID_' . $itemCount ) )
            {
                continue;
            }

            $rssExportItem = eZRSSExportItem::fetch( $http->postVariable( 'Item_ID_'.$itemCount ), true, eZRSSExport::STATUS_DRAFT );
            if( $rssExportItem == null )
            {
                continue;
            }

            // RSS is supposed to feed certain objects from the subnodes
            if ( $http->hasPostVariable( 'Item_Subnodes_'.$itemCount ) )
            {
                $rssExportItem->setAttribute( 'subnodes', 1 );
            }
            else // Do not include subnodes
            {
                $rssExportItem->setAttribute( 'subnodes', 0 );
            }

            $rssExportItem->setAttribute( 'class_id', $http->postVariable( 'Item_Class_'.$itemCount ) );
            $class = eZContentClass::fetch(  $http->postVariable( 'Item_Class_'.$itemCount ) );

            $titleClassAttributeIdentifier = $http->postVariable( 'Item_Class_Attribute_Title_'.$itemCount );
            $descriptionClassAttributeIdentifier = $http->postVariable( 'Item_Class_Attribute_Description_'.$itemCount );
            $categoryClassAttributeIdentifier = $http->postVariable( 'Item_Class_Attribute_Category_'.$itemCount );

            if ( !$class )
            {
                $validated = false;
                $validationErrors[] = ezpI18n::tr( 'kernel/rss/edit_export',
                                              'Selected class does not exist' );
            }
            else
            {
                $dataMap = $class->attribute( 'data_map' );
                if ( !isset( $dataMap[$titleClassAttributeIdentifier] ) )
                {
                    $valid = false;
                    $validationErrors[] = ezpI18n::tr( 'kernel/rss/edit_export',
                                                  'Invalid selection for title class %1 does not have attribute "%2"', null,
                                                  array( $class->attribute( 'name'), $titleClassAttributeIdentifier ) );
                }
                if ( $descriptionClassAttributeIdentifier != '' && !isset( $dataMap[$descriptionClassAttributeIdentifier] ) )
                {
                    $valid = false;
                    $validationErrors[] = ezpI18n::tr( 'kernel/rss/edit_export',
                                                  'Invalid selection for description class %1 does not have attribute "%2"', null,
                                                  array( $class->attribute( 'name'), $descriptionClassAttributeIdentifier ) );
                }
                if ( $categoryClassAttributeIdentifier != '' && !isset( $dataMap[$categoryClassAttributeIdentifier] ) )
                {
                    $valid = false;
                    $validationErrors[] = ezpI18n::tr( 'kernel/rss/edit_export',
                                                  'Invalid selection for category class %1 does not have attribute "%2"', null,
                                                  array( $class->attribute( 'name'), $categoryClassAttributeIdentifier ) );
                }
            }

            $rssExportItem->setAttribute( 'title', $http->postVariable( 'Item_Class_Attribute_Title_' . $itemCount ) );
            $rssExportItem->setAttribute( 'description', $http->postVariable( 'Item_Class_Attribute_Description_' . $itemCount ) );
            $rssExportItem->setAttribute( 'category', $http->postVariable( 'Item_Class_Attribute_Category_' . $itemCount ) );

            if ( $http->hasPostVariable( 'Item_Class_Attribute_Enclosure_' . $itemCount ) )
                $rssExportItem->setAttribute( 'enclosure', $http->postVariable( 'Item_Class_Attribute_Enclosure_' . $itemCount ) );

            if( $publish && $valid )
            {
                $rssExportItem->setAttribute( 'status', eZRSSExport::STATUS_VALID );
                $rssExportItem->store();
                // delete drafts
                $rssExportItem->setAttribute( 'status', eZRSSExport::STATUS_DRAFT );
                $rssExportItem->remove();
            }
            else
            {
                $rssExportItem->store();
            }
        }
        $rssExportID = $http->postVariable( 'RSSExport_ID' );
        $rssExport = eZRSSExport::fetch( $rssExportID, true, eZRSSExport::STATUS_DRAFT );

        // The draft can be gone by the time Save is pressed: the edit timed out
        // and the draft was collected, or it was cleared from elsewhere while
        // this page sat open. Taking the published row up again lets the save
        // land rather than throwing away everything that was typed - and saying
        // so plainly beats a fatal on a null.
        if ( !$rssExport instanceof eZRSSExport )
        {
            $rssExport = eZRSSExport::fetch( $rssExportID, true, eZRSSExport::STATUS_VALID );
            if ( $rssExport instanceof eZRSSExport )
            {
                $rssExport->setAttribute( 'status', eZRSSExport::STATUS_DRAFT );
                $rssExport->store();
            }
        }

        if ( !$rssExport instanceof eZRSSExport )
        {
            $db->commit();
            return array( 'valid' => false,
                          'published' => false,
                          'validation_errors' => array(
                              ezpI18n::tr( 'kernel/rss/edit_export',
                                           'This RSS export no longer exists. It may have been removed while this page was open.' ) ) );
        }

        $rssExport->setAttribute( 'title', $http->postVariable( 'title' ) );
        $rssExport->setAttribute( 'url', $http->postVariable( 'url' ) );
        // $rssExport->setAttribute( 'site_access', $http->postVariable( 'SiteAccess' ) );
        $rssExport->setAttribute( 'description', $http->postVariable( 'Description' ) );
        $rssExport->setAttribute( 'rss_version', $http->postVariable( 'RSSVersion' ) );
        $rssExport->setAttribute( 'number_of_objects', $http->postVariable( 'NumberOfObjects' ) );
        $rssExport->setAttribute( 'image_id', $http->postVariable( 'RSSImageID' ) );
        if ( $http->hasPostVariable( 'active' ) )
        {
            $rssExport->setAttribute( 'active', 1 );
        }
        else
        {
            $rssExport->setAttribute( 'active', 0 );
        }
        $rssExport->setAttribute( 'access_url', str_replace( array( '/', '?', '&', '>', '<' ), '',  $http->postVariable( 'Access_URL' ) ) );

        // The OPML head, and the wording of the outlines, both of which only an
        // OPML export has a page to set them on.
        if ( $isOPML )
        {
            $head = array();
            foreach ( array( 'ownerName', 'ownerEmail', 'ownerId', 'docs',
                             'expansionState', 'vertScrollState',
                             'windowTop', 'windowLeft', 'windowBottom', 'windowRight' ) as $field )
            {
                if ( !$http->hasPostVariable( 'OPMLHead_' . $field ) )
                    continue;

                $value = $http->postVariable( 'OPMLHead_' . $field );
                if ( is_scalar( $value ) )
                    $head[$field] = $value;   // setOPMLHead checks and caps each one
            }
            $rssExport->setOPMLHead( $head );

            self::storeOPMLItems( $http, $rssExport->attribute( 'id' ) );
        }

        // The podcast channel fields, which only an Apple Podcasts export has a
        // page to set them on. Checkboxes post nothing when they are off, so
        // the booleans are read from whether they arrived at all rather than
        // from their value.
        if ( $http->hasPostVariable( 'RSSVersion' ) && $http->postVariable( 'RSSVersion' ) === 'ITUNES' )
        {
            $head = array();

            foreach ( array( 'author', 'ownerName', 'ownerEmail', 'imageUrl',
                             'category', 'subcategory', 'type', 'summary',
                             'subtitle', 'copyright', 'language', 'newFeedUrl' ) as $field )
            {
                if ( !$http->hasPostVariable( 'Podcast_' . $field ) )
                    continue;

                $value = $http->postVariable( 'Podcast_' . $field );
                if ( is_scalar( $value ) )
                    $head[$field] = $value;   // setPodcastHead checks and caps each one
            }

            foreach ( array( 'explicit', 'block', 'complete' ) as $field )
                $head[$field] = $http->hasPostVariable( 'Podcast_' . $field ) ? 'true' : 'false';

            $rssExport->setPodcastHead( $head );
        }
        if ( $http->hasPostVariable( 'MainNodeOnly' ) )
        {
            $rssExport->setAttribute( 'main_node_only', 1 );
        }
        else
        {
            $rssExport->setAttribute( 'main_node_only', 0 );
        }

        $published = false;
        if ( $publish && $valid )
        {
            $rssExport->store( true );

            // The outlines follow the export: the draft rows become the valid
            // ones, and the draft is cleared out behind them.
            if ( $isOPML )
            {
                eZRSSExportOPMLItem::copyStatus( $rssExport->attribute( 'id' ),
                                                 eZRSSExport::STATUS_DRAFT,
                                                 eZRSSExport::STATUS_VALID );
            }

            // remove draft
            $rssExport->remove();
            $published = true;
        }
        else
        {
            $rssExport->store();
        }
        $db->commit();
        return array( 'valid' => $valid,
                      'published' => $published,
                      'validation_errors' => $validationErrors );
    }

    /**
     * Writes back what was typed into the outline rows of an OPML export.
     *
     * Each row on the page carries its own id, so a row removed or added since
     * the page was drawn cannot make the values land on the wrong outline.
     *
     * @param eZHTTPTool $http
     * @param int $exportID
     */
    static function storeOPMLItems( $http, $exportID )
    {
        if ( !$http->hasPostVariable( 'OPMLItem_ID' ) )
            return;

        $ids = $http->postVariable( 'OPMLItem_ID' );
        if ( !is_array( $ids ) )
            return;

        // The page never draws more rows than this, so a longer list did not
        // come from the page.
        if ( count( $ids ) > eZRSSExportOPMLItem::MAX_BULK )
            $ids = array_slice( $ids, 0, eZRSSExportOPMLItem::MAX_BULK );

        $text     = $http->hasPostVariable( 'OPMLItem_Text' ) ? $http->postVariable( 'OPMLItem_Text' ) : array();
        $title    = $http->hasPostVariable( 'OPMLItem_Title' ) ? $http->postVariable( 'OPMLItem_Title' ) : array();
        $desc     = $http->hasPostVariable( 'OPMLItem_Description' ) ? $http->postVariable( 'OPMLItem_Description' ) : array();
        $category = $http->hasPostVariable( 'OPMLItem_Category' ) ? $http->postVariable( 'OPMLItem_Category' ) : array();
        $language = $http->hasPostVariable( 'OPMLItem_Language' ) ? $http->postVariable( 'OPMLItem_Language' ) : array();
        $type     = $http->hasPostVariable( 'OPMLItem_Type' ) ? $http->postVariable( 'OPMLItem_Type' ) : array();
        $parent   = $http->hasPostVariable( 'OPMLItem_Parent' ) ? $http->postVariable( 'OPMLItem_Parent' ) : array();
        $priority = $http->hasPostVariable( 'OPMLItem_Priority' ) ? $http->postVariable( 'OPMLItem_Priority' ) : array();
        $xmlUrl   = $http->hasPostVariable( 'OPMLItem_XmlUrl' ) ? $http->postVariable( 'OPMLItem_XmlUrl' ) : array();
        $htmlUrl  = $http->hasPostVariable( 'OPMLItem_HtmlUrl' ) ? $http->postVariable( 'OPMLItem_HtmlUrl' ) : array();
        $url      = $http->hasPostVariable( 'OPMLItem_Url' ) ? $http->postVariable( 'OPMLItem_Url' ) : array();
        $comment  = $http->hasPostVariable( 'OPMLItem_IsComment' ) ? $http->postVariable( 'OPMLItem_IsComment' ) : array();
        $break    = $http->hasPostVariable( 'OPMLItem_IsBreakpoint' ) ? $http->postVariable( 'OPMLItem_IsBreakpoint' ) : array();
        $subnodes = $http->hasPostVariable( 'OPMLItem_Subnodes' ) ? $http->postVariable( 'OPMLItem_Subnodes' ) : array();

        // Every one of the above is a post variable and can arrive as anything
        // at all; a string where an array is expected would make the reads
        // below index into characters.
        foreach ( array( 'text', 'title', 'desc', 'category', 'language', 'type', 'parent',
                         'priority', 'xmlUrl', 'htmlUrl', 'url', 'comment', 'break', 'subnodes' ) as $bag )
            if ( !is_array( $$bag ) )
                $$bag = array();

        $types = array_keys( eZRSSExportOPMLItem::outlineTypes() );

        $db = eZDB::instance();
        $db->begin();
        foreach ( $ids as $itemID )
        {
            $item = eZRSSExportOPMLItem::fetch( $itemID, true, eZRSSExport::STATUS_DRAFT );
            if ( !$item || (int) $item->attribute( 'rssexport_id' ) !== (int) $exportID )
                continue;   // not ours, or gone since the page was drawn

            // Trimmed to what the column holds, and stripped of characters no
            // xml document can carry, before it is stored - not after, when a
            // strict database would already have refused the row.
            $item->setAttribute( 'outline_text', eZRSSExportOPMLItem::safeText( isset( $text[$itemID] ) ? $text[$itemID] : '' ) );
            $item->setAttribute( 'title', eZRSSExportOPMLItem::safeText( isset( $title[$itemID] ) ? $title[$itemID] : '' ) );
            $item->setAttribute( 'description', eZRSSExportOPMLItem::safeText( isset( $desc[$itemID] ) ? $desc[$itemID] : '' ) );
            $item->setAttribute( 'category', eZRSSExportOPMLItem::safeText( isset( $category[$itemID] ) ? $category[$itemID] : '' ) );
            $item->setAttribute( 'language', eZRSSExportOPMLItem::safeText( isset( $language[$itemID] ) ? $language[$itemID] : '', eZRSSExportOPMLItem::MAX_SHORT ) );

            // Addresses are checked here as well as on the way out, so an
            // address a reader must not follow is never stored in the first
            // place and cannot be seen in the edit page either.
            $item->setAttribute( 'xml_url', eZRSSExportOPMLItem::safeURL( isset( $xmlUrl[$itemID] ) ? $xmlUrl[$itemID] : '' ) );
            $item->setAttribute( 'html_url', eZRSSExportOPMLItem::safeURL( isset( $htmlUrl[$itemID] ) ? $htmlUrl[$itemID] : '' ) );
            $item->setAttribute( 'url', eZRSSExportOPMLItem::safeURL( isset( $url[$itemID] ) ? $url[$itemID] : '' ) );

            if ( isset( $type[$itemID] ) && in_array( $type[$itemID], $types, true ) )
                $item->setAttribute( 'outline_type', $type[$itemID] );

            if ( isset( $priority[$itemID] ) && is_scalar( $priority[$itemID] ) && is_numeric( $priority[$itemID] ) )
                $item->setAttribute( 'priority', max( 0, min( 999999, (int) $priority[$itemID] ) ) );

            // A line cannot be its own parent, and a parent it does not share an
            // export with would put it in somebody else's document.
            if ( isset( $parent[$itemID] ) && is_scalar( $parent[$itemID] ) && is_numeric( $parent[$itemID] ) )
            {
                $parentID = (int) $parent[$itemID];
                if ( $parentID === (int) $itemID )
                    $parentID = 0;
                if ( $parentID )
                {
                    $parentItem = eZRSSExportOPMLItem::fetch( $parentID, true, eZRSSExport::STATUS_DRAFT );
                    if ( !$parentItem || (int) $parentItem->attribute( 'rssexport_id' ) !== (int) $exportID )
                        $parentID = 0;
                }
                $item->setAttribute( 'parent_id', $parentID );
            }

            $item->setAttribute( 'is_comment', isset( $comment[$itemID] ) ? 1 : 0 );
            $item->setAttribute( 'is_breakpoint', isset( $break[$itemID] ) ? 1 : 0 );
            $item->setAttribute( 'subnodes', isset( $subnodes[$itemID] ) ? 1 : 0 );

            $item->store();
        }
        $db->commit();
    }

    /**
     * Set RSSExportItem defaults based on site.ini [RSSSettings] settings
     *
     * @param eZRSSExportItem $rssExportItem
     * @return bool True if changes where made
     */
    static function setItemDefaults( eZRSSExportItem $rssExportItem )
    {
        $nodeId = $rssExportItem->attribute( 'source_node_id' );
        $node = $nodeId ? eZContentObjectTreeNode::fetch( $nodeId ) : null;

        if ( !$node instanceof eZContentObjectTreeNode )
            return false;

        $config = eZINI::instance( 'site.ini' );
        $nodeClassIdentifier =  $node->attribute( 'class_identifier' );
        $defaultFeedItemClasses = $config->variable( 'RSSSettings', 'DefaultFeedItemClasses' );
        if ( !isset( $defaultFeedItemClasses[$nodeClassIdentifier] ) )
            return false;

        $feedItemClasses = explode( ';', $defaultFeedItemClasses[$nodeClassIdentifier] );
        $iniSection = 'RSSSettings_' . $feedItemClasses[0];
        if ( !$config->hasVariable( $iniSection, 'FeedObjectAttributeMap' ) )
            return false;

        $feedObjectAttributeMap = $config->variable( $iniSection, 'FeedObjectAttributeMap' );
        $subNodesMap = $config->hasVariable( $iniSection, 'Subnodes' ) ? $config->variable( $iniSection, 'Subnodes' ) : array();

        $rssExportItem->setAttribute( 'class_id', eZContentObjectTreeNode::classIDByIdentifier( $feedItemClasses[0] ) );
        $rssExportItem->setAttribute( 'title', $feedObjectAttributeMap['title'] );
        if ( isset( $feedObjectAttributeMap['description'] ) )
            $rssExportItem->setAttribute( 'description', $feedObjectAttributeMap['description'] );

        if ( isset( $feedObjectAttributeMap['category'] ) )
            $rssExportItem->setAttribute( 'category', $feedObjectAttributeMap['category'] );

        if ( isset( $feedObjectAttributeMap['enclosure'] ) )
            $rssExportItem->setAttribute( 'enclosure', $feedObjectAttributeMap['enclosure'] );

        $rssExportItem->setAttribute( 'subnodes', isset( $subNodesMap[$nodeClassIdentifier] ) && $subNodesMap[$nodeClassIdentifier] === 'true' );
        $rssExportItem->store();
    }
}
?>
