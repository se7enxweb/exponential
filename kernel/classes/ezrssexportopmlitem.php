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
    /**
     * How many rows one press of a button may add or remove.
     *
     * The browser never offers more than its largest page, so anything beyond
     * this did not come from the page. Refusing the excess keeps a hand made
     * post from turning one click into ten thousand queries.
     */
    const MAX_BULK = 250;

    /**
     * The width of the text columns. Anything longer is cut here rather than by
     * the database, which on a strict server refuses the row instead of
     * trimming it, and the save would fail with nothing to show for it.
     */
    const MAX_TEXT = 255;

    /**
     * Shorter columns, kept in step with the schema.
     */
    const MAX_SHORT = 50;

    /**
     * How deep outlines may nest before the document stops going down.
     *
     * A chain longer than this is not a document anyone meant to write, and
     * following it costs a stack frame a level.
     */
    const MAX_DEPTH = 20;

    /**
     * The address schemes an outline may point at.
     *
     * An OPML document is read by other people's software, which follows what
     * it finds. javascript:, data: and file: addresses have no business in a
     * subscription list, so they are dropped rather than written out.
     */
    const ALLOWED_SCHEMES = 'http,https';

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
                                                      // Without these two the edit page cannot see a
                                                      // chosen node: sourceNode() and sourcePath() were
                                                      // written and never exposed, so the row went on
                                                      // offering "Browse content" after a node had been
                                                      // picked and stored.
                                                      'source_node' => 'sourceNode',
                                                      'source_path' => 'sourcePath',
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
        $exportId = (int) $exportId;
        $already  = self::selectedTargetIDs( $exportId, $status );

        // Whittle the list down before touching the database at all. Nothing
        // that is not a number, nothing twice, never itself - an export listing
        // itself is a loop for whatever reads it - and never more than a page
        // full, because a longer list did not come from the page.
        $wanted = array();
        foreach ( $targetIDs as $targetID )
        {
            if ( !is_scalar( $targetID ) || !is_numeric( $targetID ) )
                continue;

            $targetID = (int) $targetID;
            if ( $targetID <= 0 || $targetID === $exportId || isset( $already[$targetID] ) || isset( $wanted[$targetID] ) )
                continue;

            $wanted[$targetID] = $targetID;
            if ( count( $wanted ) >= self::MAX_BULK )
                break;
        }

        if ( !count( $wanted ) )
            return 0;

        // One query to find out which of them are real, rather than one each.
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT id FROM ezrss_export WHERE status=' . (int) eZRSSExport::STATUS_VALID
                                 . ' AND id IN (' . implode( ',', $wanted ) . ')' );

        $real = array();
        foreach ( $rows as $row )
            $real[] = (int) $row['id'];

        if ( !count( $real ) )
            return 0;

        $priority = self::nextPriority( $exportId, $status );
        $added    = 0;

        $db->begin();
        foreach ( $real as $targetID )
        {
            $item = self::create( $exportId, $targetID, $priority++ );
            $item->setAttribute( 'status', $status );
            $item->store();
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
        {
            if ( !is_scalar( $itemID ) || !is_numeric( $itemID ) || (int) $itemID <= 0 )
                continue;

            $clean[(int) $itemID] = (int) $itemID;
            if ( count( $clean ) >= self::MAX_BULK )
                break;   // more than a page full did not come from the page
        }

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

        // An export that is not active is not served, so listing it would hand
        // a reader an address that answers with an error - and would publish
        // the name of a feed somebody has deliberately taken out of service.
        if ( $target && !$target->attribute( 'active' ) )
            $target = null;

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

            // Built against this document's own site, the same way the feed
            // branch above builds its addresses. transformURI( 'full' ) was
            // wrong here: it prefixes the host of the request doing the
            // generating, which is the administration host whenever a feed is
            // looked at or regenerated from the admin, so a reader was handed
            // an address on a site they have no business reaching.
            $nodeURL = self::absolute( $baseURL, $node->attribute( 'url_alias' ) );

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

        // Everything that goes into the document goes through the two guards
        // above: text that xml can carry, and addresses a reader can follow.
        $xmlUrl  = self::safeURL( $xmlUrl );
        $htmlUrl = self::safeURL( $htmlUrl );
        $url     = self::safeURL( $url );

        // Checked a second time: the guard may have just emptied the address
        // that made the line worth writing.
        if ( $type === 'rss' && $xmlUrl === '' )
            return null;
        if ( ( $type === 'link' || $type === 'include' ) && $url === '' )
            return null;

        return array( 'type'          => self::safeText( $type, self::MAX_SHORT ),
                      'text'          => self::safeText( $text ),
                      'title'         => self::safeText( $title ),
                      'description'   => self::safeText( $description ),
                      'category'      => self::safeText( $this->Category ),
                      'language'      => self::safeText( $this->Language, self::MAX_SHORT ),
                      'xmlUrl'        => $xmlUrl,
                      'htmlUrl'       => $htmlUrl,
                      'url'           => $url,
                      'version'       => self::safeText( $version, self::MAX_SHORT ),
                      'isComment'     => (bool) $this->IsComment,
                      'isBreakpoint'  => (bool) $this->IsBreakpoint,
                      'created'       => (int) $this->Created );
    }

    /**
     * Text that can be written into a document without breaking it.
     *
     * XML 1.0 has no way to carry most control characters - not escaped, not
     * as a reference - so a stray one stored years ago would produce a document
     * that nothing can parse. They are dropped here, on the way out and on the
     * way in, along with any length the column cannot hold.
     *
     * @param mixed $value
     * @param int $max longest result, in characters.
     * @return string
     */
    static function safeText( $value, $max = self::MAX_TEXT )
    {
        if ( is_array( $value ) || is_object( $value ) )
            return '';

        $value = (string) $value;

        // Tab, newline and carriage return are the only control characters XML
        // allows; the rest go, whatever encoding they arrived in.
        $value = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value );
        if ( $value === null )   // invalid utf-8 makes preg_replace give up
            return '';

        $value = trim( $value );

        if ( $max > 0 && mb_strlen( $value, 'UTF-8' ) > $max )
            $value = mb_substr( $value, 0, $max, 'UTF-8' );

        return $value;
    }

    /**
     * An address that is safe to put in front of somebody else's reader.
     *
     * Only the schemes in ALLOWED_SCHEMES survive, plus addresses relative to
     * the site. Anything else - javascript:, data:, file:, a scheme nobody has
     * heard of - comes back empty, and the caller leaves the attribute out.
     *
     * @param mixed $value
     * @param int $max
     * @return string
     */
    static function safeURL( $value, $max = self::MAX_TEXT )
    {
        $value = self::safeText( $value, $max );
        if ( $value === '' )
            return '';

        // Whitespace inside an address is never meaningful and is how a scheme
        // gets smuggled past a check: "java\nscript:alert(1)".
        $value = preg_replace( '/\s+/u', '', $value );
        if ( $value === null || $value === '' )
            return '';

        // Relative to the site, or to the protocol. Neither can name a scheme.
        if ( strpos( $value, '//' ) === 0 || strpos( $value, '/' ) === 0 )
            return $value;

        $colon = strpos( $value, ':' );
        if ( $colon === false )
            return $value;   // a bare path

        $scheme = strtolower( substr( $value, 0, $colon ) );

        return in_array( $scheme, explode( ',', self::ALLOWED_SCHEMES ), true ) ? $value : '';
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
    static function fetchTree( $exportId, $status = eZRSSExport::STATUS_VALID, $limit = false )
    {
        $items = self::fetchList( $exportId, $status );

        // A document with more lines than this is not one anybody is reading;
        // the rest are left out rather than held in memory to be written.
        if ( $limit !== false && count( $items ) > (int) $limit )
            $items = array_slice( $items, 0, (int) $limit );

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
