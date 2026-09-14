<?php
/**
 * File containing the eZRSSExportOPMLItem class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * One line of an OPML export: a feed that the export lists.
 *
 * An OPML document is a list of feeds rather than a list of articles, so an
 * OPML export has no content source and no class mapping. What it has instead
 * is a set of these, each naming another RSS export on this installation.
 *
 * The address is worked out when the document is written, from the export this
 * row points at, so renaming a feed's access url cannot leave a dead entry
 * behind. The text columns are overrides: left empty, the target's own title
 * and description are used.
 *
 * Rows follow the same draft and valid statuses as the rest of the RSS system,
 * so editing an OPML export is as reversible as editing any other.
 */
class eZRSSExportOPMLItem extends eZPersistentObject
{
    public $ID;
    public $RSSExportID;
    public $ParentID;
    public $TargetExportID;
    public $SourceNodeID;
    public $Subnodes;
    public $OutlineType;
    public $Priority;
    public $Text;
    public $Title;
    public $Description;
    public $Category;
    public $XmlURL;
    public $HtmlURL;
    public $URL;
    public $Language;
    public $IsComment;
    public $IsBreakpoint;
    public $Created;
    public $Status;

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'rssexport_id' => array( 'name' => 'RSSExportID',
                                                                  'datatype' => 'integer',
                                                                  'default' => 0,
                                                                  'required' => true,
                                                                  'foreign_class' => 'eZRSSExport',
                                                                  'foreign_attribute' => 'id',
                                                                  'multiplicity' => '1..*' ),
                                         'parent_id' => array( 'name' => 'ParentID',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'target_export_id' => array( 'name' => 'TargetExportID',
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true ),
                                         'source_node_id' => array( 'name' => 'SourceNodeID',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
                                         'subnodes' => array( 'name' => 'Subnodes',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'outline_type' => array( 'name' => 'OutlineType',
                                                                  'datatype' => 'string',
                                                                  'default' => 'rss',
                                                                  'required' => true ),
                                         'priority' => array( 'name' => 'Priority',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'outline_text' => array( 'name' => 'Text',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'title' => array( 'name' => 'Title',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => false ),
                                         'description' => array( 'name' => 'Description',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         'category' => array( 'name' => 'Category',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => false ),
                                         'xml_url' => array( 'name' => 'XmlURL',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => false ),
                                         'html_url' => array( 'name' => 'HtmlURL',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => false ),
                                         'url' => array( 'name' => 'URL',
                                                         'datatype' => 'string',
                                                         'default' => '',
                                                         'required' => false ),
                                         'language' => array( 'name' => 'Language',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => false ),
                                         'is_comment' => array( 'name' => 'IsComment',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                         'is_breakpoint' => array( 'name' => 'IsBreakpoint',
                                                                   'datatype' => 'integer',
                                                                   'default' => 0,
                                                                   'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ) ),
                      'keys' => array( 'id', 'status' ),
                      'function_attributes' => array( 'target'      => 'targetExport',
                                                      'outline'     => 'outline',
                                                      'target_gone' => 'targetGone' ),
                      'increment_key' => 'id',
                      'sort' => array( 'priority' => 'asc' ),
                      'class_name' => 'eZRSSExportOPMLItem',
                      'name' => 'ezrss_export_opml_item' );
    }

    /**
     * A new line for an OPML export, pointing at one of the other exports.
     *
     * @param int $rssexport_id the OPML export this belongs to.
     * @param int $target_export_id the export it lists.
     * @param int $priority where it sits in the document.
     * @return eZRSSExportOPMLItem
     */
    static function create( $rssexport_id, $target_export_id = 0, $priority = 0 )
    {
        return new eZRSSExportOPMLItem( array( 'id' => null,
                                               'rssexport_id' => $rssexport_id,
                                               'parent_id' => 0,
                                               'target_export_id' => $target_export_id,
                                               'source_node_id' => 0,
                                               'subnodes' => 0,
                                               'outline_type' => $target_export_id ? 'rss' : 'group',
                                               'priority' => $priority,
                                               'outline_text' => '',
                                               'title' => '',
                                               'description' => '',
                                               'category' => '',
                                               'xml_url' => '',
                                               'html_url' => '',
                                               'url' => '',
                                               'language' => '',
                                               'is_comment' => 0,
                                               'is_breakpoint' => 0,
                                               'created' => 0,
                                               'status' => eZRSSExport::STATUS_DRAFT ) );
    }

    static function fetch( $id, $asObject = true, $status = eZRSSExport::STATUS_VALID )
    {
        return eZPersistentObject::fetchObject( self::definition(), null,
                                                array( 'id' => $id, 'status' => $status ),
                                                $asObject );
    }

    /**
     * The lines of one OPML export, in the order they appear in the document.
     *
     * @param int $exportId
     * @param int $status draft or valid.
     * @return array of eZRSSExportOPMLItem
     */
    static function fetchList( $exportId, $status = eZRSSExport::STATUS_VALID )
    {
        return eZPersistentObject::fetchObjectList(
            self::definition(), null,
            array( 'rssexport_id' => (int) $exportId, 'status' => (int) $status ),
            array( 'priority' => 'asc', 'id' => 'asc' ) );
    }

    /**
     * How many feeds an OPML export lists, without fetching them.
     *
     * @param int $exportId
     * @param int $status
     * @return int
     */
    static function fetchListCount( $exportId, $status = eZRSSExport::STATUS_VALID )
    {
        return (int) eZPersistentObject::count(
            self::definition(),
            array( 'rssexport_id' => (int) $exportId, 'status' => (int) $status ) );
    }

    /**
     * The exports already listed, so the browser can show which are in.
     *
     * @param int $exportId
     * @param int $status
     * @return array of int, keyed by the same int for quick lookup.
     */
    static function selectedTargetIDs( $exportId, $status = eZRSSExport::STATUS_VALID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT target_export_id FROM ezrss_export_opml_item WHERE rssexport_id='
                                 . (int) $exportId . ' AND status=' . (int) $status );
        $ids = array();
        foreach ( $rows as $row )
            $ids[(int) $row['target_export_id']] = (int) $row['target_export_id'];

        return $ids;
    }

    /**
     * The next free position in the document.
     *
     * @param int $exportId
     * @param int $status
     * @return int
     */
    static function nextPriority( $exportId, $status = eZRSSExport::STATUS_DRAFT )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT MAX(priority) AS p FROM ezrss_export_opml_item WHERE rssexport_id='
                                 . (int) $exportId . ' AND status=' . (int) $status );

        return count( $rows ) && $rows[0]['p'] !== null ? (int) $rows[0]['p'] + 1 : 0;
    }

    /**
     * Adds feeds to an OPML export, skipping ones it already lists.
     *
     * @param int $exportId
     * @param array $targetIDs export ids to add.
     * @param int $status
     * @return int how many were actually added.
     */
    static function addTargets( $exportId, array $targetIDs, $status = eZRSSExport::STATUS_DRAFT )
    {
        $already  = self::selectedTargetIDs( $exportId, $status );
        $priority = self::nextPriority( $exportId, $status );
        $added    = 0;

        $db = eZDB::instance();
        $db->begin();
        foreach ( $targetIDs as $targetID )
        {
            $targetID = (int) $targetID;

            // Nothing that does not exist, nothing twice, and never itself:
            // an OPML export listing itself is a loop for whatever reads it.
            if ( $targetID <= 0 || $targetID == (int) $exportId || isset( $already[$targetID] ) )
                continue;
            if ( !eZRSSExport::fetch( $targetID, true, eZRSSExport::STATUS_VALID ) )
                continue;

            $item = self::create( $exportId, $targetID, $priority++ );
            $item->setAttribute( 'status', $status );
            $item->store();

            $already[$targetID] = $targetID;
            $added++;
        }
        $db->commit();

        return $added;
    }

    /**
     * Removes lines from an OPML export, in both statuses.
     *
     * @param int $exportId
     * @param array $itemIDs
     * @return int how many rows went.
     */
    static function removeItems( $exportId, array $itemIDs )
    {
        $clean = array();
        foreach ( $itemIDs as $itemID )
            if ( (int) $itemID > 0 )
                $clean[] = (int) $itemID;

        if ( !count( $clean ) )
            return 0;

        $db = eZDB::instance();
        $db->begin();
        $db->query( 'DELETE FROM ezrss_export_opml_item WHERE rssexport_id=' . (int) $exportId
                    . ' AND id IN (' . implode( ',', $clean ) . ')' );
        $db->commit();

        return count( $clean );
    }

    /**
     * Removes an OPML export's lines.
     *
     * The status matters: throwing away a draft must leave the published
     * document alone, or cancelling an edit - or simply letting the draft time
     * out - would empty the feed that is live.
     *
     * @param int $exportId
     * @param int|false $status one status, or false for every one of them.
     */
    static function removeByExport( $exportId, $status = false )
    {
        $where = 'rssexport_id=' . (int) $exportId;
        if ( $status !== false )
            $where .= ' AND status=' . (int) $status;

        $db = eZDB::instance();
        $db->query( 'DELETE FROM ezrss_export_opml_item WHERE ' . $where );
    }

    /**
     * Copies one status of an export's lines onto another.
     *
     * Used when an export is opened for editing - the valid rows become the
     * draft the edit works on - and when it is published, the other way round.
     *
     * @param int $exportId
     * @param int $fromStatus
     * @param int $toStatus
     */
    static function copyStatus( $exportId, $fromStatus, $toStatus )
    {
        $db = eZDB::instance();
        $db->begin();
        $db->query( 'DELETE FROM ezrss_export_opml_item WHERE rssexport_id=' . (int) $exportId
                    . ' AND status=' . (int) $toStatus );

        foreach ( self::fetchList( $exportId, $fromStatus ) as $item )
        {
            $copy = self::create( $exportId,
                                  $item->attribute( 'target_export_id' ),
                                  $item->attribute( 'priority' ) );
            $copy->setAttribute( 'parent_id', $item->attribute( 'parent_id' ) );
            foreach ( self::copiedFields() as $field )
                $copy->setAttribute( $field, $item->attribute( $field ) );
            $copy->setAttribute( 'status', $toStatus );
            $copy->store();
        }
        $db->commit();
    }

    /**
     * The columns that describe the outline rather than identify the row.
     *
     * Everything an edit can change, and so everything a draft has to carry
     * over when it is published or taken up again.
     *
     * @return array of field name.
     */
    static function copiedFields()
    {
        return array( 'outline_type', 'outline_text', 'title', 'description', 'category',
                      'xml_url', 'html_url', 'url', 'language',
                      'source_node_id', 'subnodes',
                      'is_comment', 'is_breakpoint', 'created' );
    }

    /**
     * The outline types OPML describes, and this page can write.
     *
     * @return array type => a word for it in the interface.
     */
    static function outlineTypes()
    {
        return array( 'rss'     => 'Feed',
                      'link'    => 'Link',
                      'include' => 'Included outline',
                      'group'   => 'Group',
                      'text'    => 'Text' );
    }

    /**
     * The export this line points at, or null if it has since been removed.
     *
     * @return eZRSSExport|null
     */
    function targetExport()
    {
        if ( !$this->TargetExportID )
            return null;

        $target = eZRSSExport::fetch( $this->TargetExportID, true, eZRSSExport::STATUS_VALID );

        return $target ? $target : null;
    }

    /**
     * Whether the feed this line names has been deleted since it was added.
     *
     * @return bool
     */
    function targetGone()
    {
        return $this->TargetExportID && !$this->targetExport();
    }

    /**
     * The node this line draws on, when it is fed by content rather than a feed.
     *
     * @return eZContentObjectTreeNode|null
     */
    function sourceNode()
    {
        if ( !$this->SourceNodeID )
            return null;

        $node = eZContentObjectTreeNode::fetch( $this->SourceNodeID );

        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * Where that node sits, for the edit page to show beside the browse button.
     *
     * @return string
     */
    function sourcePath()
    {
        $node = $this->sourceNode();
        if ( !$node )
            return '';

        $names = array();
        foreach ( (array) $node->attribute( 'path_array' ) as $nodeID )
        {
            $step = eZContentObjectTreeNode::fetch( $nodeID, false, false );
            if ( is_array( $step ) && isset( $step['name'] ) )
                $names[] = $step['name'];
        }

        return implode( '/', $names );
    }

    /**
     * The line as OPML says it: text, xmlUrl and the rest, ready to write out.
     *
     * Anything filled in on the edit page wins; what is left empty is taken
     * from whatever the line is fed by - the feed it lists, or the content node
     * it points at - so the document keeps up with them.
     *
     * @param string $baseURL the site address the feed addresses hang off.
     * @return array|null null when there is nothing left to say.
     */
    function outline( $baseURL = '' )
    {
        $type   = $this->OutlineType !== '' ? $this->OutlineType : 'rss';
        $target = $this->targetExport();
        $node   = $this->sourceNode();

        $text        = $this->Text;
        $title       = $this->Title;
        $description = $this->Description;
        $xmlUrl      = $this->XmlURL;
        $htmlUrl     = $this->HtmlURL;
        $url         = $this->URL;
        $version     = '';

        if ( $target )
        {
            if ( $text === '' )        $text = $target->attribute( 'title' );
            if ( $description === '' ) $description = $target->attribute( 'description' );

            // The address is built from the listed feed's own site, not from
            // this document's, so a feed belonging to another site in the same
            // installation is still pointed at correctly - and a site url that
            // someone has filled in with a feed address instead cannot produce
            // one address inside another.
            if ( $xmlUrl === '' )
            {
                $targetSite = self::siteOf( $target->attribute( 'url' ) );
                $xmlUrl = self::absolute( $targetSite !== '' ? $targetSite : $baseURL,
                                          'rss/feed/' . $target->attribute( 'access_url' ) );
            }

            if ( $htmlUrl === '' ) $htmlUrl = self::siteOf( $target->attribute( 'url' ) );
            $version = self::opmlVersionOf( $target->attribute( 'rss_version' ) );
        }
        else if ( $node )
        {
            // Fed by content: the node's name and its address, which is what a
            // reader following a link outline is going to want.
            if ( $text === '' ) $text = $node->attribute( 'name' );
            $nodeURL = $node->attribute( 'url_alias' );
            eZURI::transformURI( $nodeURL, false, 'full' );
            if ( $url === '' )     $url = $nodeURL;
            if ( $htmlUrl === '' ) $htmlUrl = $nodeURL;
        }

        if ( $title === '' )
            $title = $text;

        // An rss outline is required to carry an address; without one there is
        // no feed to point at, so the line is left out rather than written as
        // something a reader cannot use.
        if ( $type === 'rss' && $xmlUrl === '' )
            return null;
        if ( ( $type === 'link' || $type === 'include' ) && $url === '' )
            return null;
        if ( $text === '' )
            $text = $xmlUrl !== '' ? $xmlUrl : $url;
        if ( $text === '' )
            return null;

        return array( 'type'          => $type,
                      'text'          => $text,
                      'title'         => $title,
                      'description'   => $description,
                      'category'      => $this->Category,
                      'language'      => $this->Language,
                      'xmlUrl'        => $xmlUrl,
                      'htmlUrl'       => $htmlUrl,
                      'url'           => $url,
                      'version'       => $version,
                      'isComment'     => (bool) $this->IsComment,
                      'isBreakpoint'  => (bool) $this->IsBreakpoint,
                      'created'       => (int) $this->Created );
    }

    /**
     * The site part of an address, with any feed path taken back off it.
     *
     * The url column of an export means the site the feed belongs to, but it is
     * a free text field and a feed's own address does get typed into it. Left
     * as it stands, that address would be used as a base and the feed path
     * added to it a second time.
     *
     * @param string $url
     * @return string
     */
    static function siteOf( $url )
    {
        $url = trim( (string) $url );
        if ( $url === '' )
            return '';

        $at = strpos( $url, '/rss/feed/' );

        return $at === false ? rtrim( $url, '/' ) : rtrim( substr( $url, 0, $at ), '/' );
    }

    /**
     * Joins a path onto the site address without doubling or losing the slash.
     *
     * @param string $baseURL
     * @param string $path
     * @return string
     */
    static function absolute( $baseURL, $path )
    {
        $baseURL = rtrim( (string) $baseURL, '/' );
        $path    = ltrim( (string) $path, '/' );

        return $baseURL === '' ? '/' . $path : $baseURL . '/' . $path;
    }

    /**
     * The lines of an export arranged as OPML nests them.
     *
     * A line whose parent is another line is written inside it, which is how
     * OPML expresses a folder of feeds. A parent that has gone missing leaves
     * its children at the top rather than dropping them.
     *
     * @param int $exportId
     * @param int $status
     * @return array of array( 'item' => eZRSSExportOPMLItem, 'children' => array( ... ) )
     */
    static function fetchTree( $exportId, $status = eZRSSExport::STATUS_VALID )
    {
        $items = self::fetchList( $exportId, $status );

        $byId = array();
        foreach ( $items as $item )
            $byId[(int) $item->attribute( 'id' )] = array( 'item' => $item, 'children' => array() );

        $tree = array();
        foreach ( $items as $item )
        {
            $id     = (int) $item->attribute( 'id' );
            $parent = (int) $item->attribute( 'parent_id' );

            // A line cannot be its own parent, and a parent that is no longer
            // there must not take its children down with it.
            if ( $parent && $parent !== $id && isset( $byId[$parent] ) )
                $byId[$parent]['children'][] =& $byId[$id];
            else
                $tree[] =& $byId[$id];
        }

        return $tree;
    }

    /**
     * What OPML calls the feed formats this system writes.
     *
     * @param string $rssVersion
     * @return string the value for the outline's version attribute.
     */
    static function opmlVersionOf( $rssVersion )
    {
        switch ( $rssVersion )
        {
            case '1.0': return 'RSS1';
            case '2.0': return 'RSS2';
            case 'ATOM': return 'ATOM';
        }

        return '';
    }
}
