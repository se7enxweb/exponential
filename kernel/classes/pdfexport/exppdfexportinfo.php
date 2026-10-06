<?php
/**
 * File containing the expPDFExportInfo class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the PDF export pages (pdf/list and its removal confirmation) say about an export beyond its row: how it is
 * produced, whether its source node still exists, which classes a tree includes, its stored file, what can be done
 * with it and what needs attention; the overview figures; the search, filter and order of the list; and the
 * drafts of new exports that were never finished.
 *
 * The *Of() methods work on plain values and read nothing, so they are tested without a database
 * (tests/tests/kernel/classes/expPDFExportTest.php). Guide: doc/guides/pdf-exports.md
 */
class expPDFExportInfo
{
    /** The orders the list offers, and the field each one compares */
    const SORT_FIELDS = array( 'title', 'modified', 'generated', 'size', 'id' );

    /** The filters the list offers */
    const FILTERS = array( 'stored', 'onthefly', 'generated', 'attention' );

    /**
     * How an export is produced, as a key: 'stored' (generated once into a file), 'onthefly' (generated for every
     * download) or 'unknown' (a status this version does not know, such as the 0 of an unfinished draft).
     *
     * @param int $status
     * @return string
     */
    public static function statusKey( $status )
    {
        switch ( (int)$status )
        {
            case eZPDFExport::CREATE_ONCE:  return 'stored';
            case eZPDFExport::CREATE_ONFLY: return 'onthefly';
        }
        return 'unknown';
    }

    /**
     * Ids from a form: whole positive numbers, once each, in the order given.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function idList( $value )
    {
        $ids = array();
        foreach ( is_array( $value ) ? $value : array( $value ) as $id )
        {
            if ( is_int( $id ) || ( is_string( $id ) && ctype_digit( $id ) ) )
            {
                $id = (int)$id;
                if ( $id > 0 && !in_array( $id, $ids, true ) )
                    $ids[] = $id;
            }
        }
        return $ids;
    }

    /**
     * What a card shows, from plain values.
     *
     * @param array $row the export's attributes: id, title, status, source_node_id, export_structure,
     *                   export_classes, pdf_filename, show_frontpage, show_footer, footer_text, modified, created
     * @param array|null $node the source node: name, url, class_name; null when it does not exist
     * @param array $file the stored file, as expPDFExportFile::facts() describes it
     * @param array $classNames class id => name, for the classes of export_classes
     * @param array $extra modifier_name, draft (bool: somebody has the export open), draft_by
     * @return array
     */
    public static function infoOf( array $row, $node, array $file, array $classNames, array $extra = array() )
    {
        $c = 'design/admin/pdf/list';
        $status = self::statusKey( isset( $row['status'] ) ? $row['status'] : 0 );
        $stored = $status === 'stored';
        $nodeID = isset( $row['source_node_id'] ) ? (int)$row['source_node_id'] : 0;
        $sourceExists = is_array( $node );
        $tree = isset( $row['export_structure'] ) && $row['export_structure'] === 'tree';
        $fileName = isset( $row['pdf_filename'] ) ? (string)$row['pdf_filename'] : '';
        $safeName = expPDFExportFile::isSafeName( $fileName );

        $classes = array();
        foreach ( expPDFExportGenerator::classArray( isset( $row['export_classes'] ) ? $row['export_classes'] : '' ) as $classID )
        {
            $classes[] = array( 'id' => (int)$classID,
                                'name' => isset( $classNames[$classID] ) && $classNames[$classID] !== ''
                                          ? (string)$classNames[$classID] : '#' . $classID,
                                'exists' => isset( $classNames[$classID] ) && $classNames[$classID] !== '' );
        }

        $fileExists = $stored && !empty( $file['exists'] );
        $problem = expPDFExportGenerator::problemOf( $nodeID, $sourceExists, $stored, $fileName );

        $warnings = array();
        if ( $problem === 'no_source' )
            $warnings[] = \ezpI18n::tr( $c, 'No source node is chosen: there is nothing to export.' );
        elseif ( $problem === 'source_missing' )
            $warnings[] = \ezpI18n::tr( $c, 'The source node %id no longer exists. Choose another one, or remove the export.', null, array( '%id' => $nodeID ) );
        if ( $stored && !$safeName )
            $warnings[] = \ezpI18n::tr( $c, 'The file name cannot be used for a stored file. Give the export a file name of letters, digits, dots, dashes and underscores.' );
        elseif ( $stored && !$fileExists )
            $warnings[] = \ezpI18n::tr( $c, 'The file has not been generated, or it was removed. Regenerate it.' );
        if ( $tree && !$classes )
            $warnings[] = \ezpI18n::tr( $c, 'No class is chosen for the tree: only the source node itself is exported.' );
        $missingClasses = 0;
        foreach ( $classes as $class )
            if ( !$class['exists'] )
                $missingClasses++;
        if ( $missingClasses > 0 )
            $warnings[] = \ezpI18n::tr( $c, '%count of the chosen classes no longer exist.', null, array( '%count' => $missingClasses ) );

        $title = isset( $row['title'] ) ? (string)$row['title'] : '';
        $search = array( $title, $fileName, isset( $row['id'] ) ? $row['id'] : '', $nodeID,
                         $sourceExists && isset( $node['name'] ) ? $node['name'] : '' );
        foreach ( $classes as $class )
            $search[] = $class['name'];

        return array(
            'id' => isset( $row['id'] ) ? (int)$row['id'] : 0,
            'title' => $title,
            'status' => $status,
            'is_stored' => $stored,
            'source_node_id' => $nodeID,
            'source_exists' => $sourceExists,
            'source_name' => $sourceExists && isset( $node['name'] ) ? (string)$node['name'] : '',
            'source_url' => $sourceExists && isset( $node['url'] ) ? (string)$node['url'] : '',
            'source_class' => $sourceExists && isset( $node['class_name'] ) ? (string)$node['class_name'] : '',
            'is_tree' => $tree,
            'classes' => $classes,
            'file_name' => $fileName,
            'file_safe' => $safeName,
            'file_exists' => $fileExists,
            'file_size' => $fileExists ? (int)$file['size'] : 0,
            'file_mtime' => $fileExists ? (int)$file['mtime'] : 0,
            'download_name' => expPDFExportFile::downloadName( $fileName, $title ),
            // The address the web server serves the stored file at (.htaccess_root and Velocity's static paths list
            // var/*/storage/pdf/<name>.pdf); relative to the installation root, for the template's ezroot.
            'public_url' => $fileExists && !empty( $file['path'] ) ? '/' . ltrim( (string)$file['path'], '/' ) : '',
            'can_download' => $stored ? $fileExists : ( $status === 'onthefly' && $problem === false ),
            'can_regenerate' => $stored && $problem === false,
            'problem' => $problem,
            'show_frontpage' => !empty( $row['show_frontpage'] ),
            'show_footer' => !empty( $row['show_footer'] ),
            'footer_text' => isset( $row['footer_text'] ) ? (string)$row['footer_text'] : '',
            'modified' => isset( $row['modified'] ) ? (int)$row['modified'] : 0,
            'created' => isset( $row['created'] ) ? (int)$row['created'] : 0,
            'modifier_name' => isset( $extra['modifier_name'] ) ? (string)$extra['modifier_name'] : '',
            'draft' => !empty( $extra['draft'] ),
            'draft_by' => isset( $extra['draft_by'] ) ? (string)$extra['draft_by'] : '',
            'warnings' => $warnings,
            'attention' => count( $warnings ) > 0,
            'search' => mb_strtolower( implode( ' ', $search ) ),
        );
    }

    /**
     * What a card shows about $export (see infoOf()), read from the database and the stored file.
     *
     * @param eZPDFExport $export
     * @param array $classNames a cache of class names, filled as classes are read
     * @return array
     */
    public static function info( $export, array &$classNames = array() )
    {
        $row = array();
        foreach ( array( 'id', 'title', 'status', 'source_node_id', 'export_structure', 'export_classes', 'pdf_filename',
                         'show_frontpage', 'show_footer', 'footer_text', 'modified', 'created' ) as $name )
            $row[$name] = $export->attribute( $name );

        $node = null;
        $nodeID = (int)$row['source_node_id'];
        $treeNode = $nodeID > 0 ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
        if ( $treeNode instanceof eZContentObjectTreeNode )
            $node = array( 'name' => (string)$treeNode->attribute( 'name' ),
                           'url' => (string)$treeNode->attribute( 'url_alias' ),
                           'class_name' => (string)$treeNode->attribute( 'class_name' ) );

        foreach ( expPDFExportGenerator::classArray( $row['export_classes'] ) as $classID )
        {
            if ( !array_key_exists( $classID, $classNames ) )
            {
                $class = eZContentClass::fetch( (int)$classID );
                $classNames[$classID] = $class instanceof eZContentClass ? (string)$class->attribute( 'name' ) : '';
            }
        }

        $stored = (int)$row['status'] === eZPDFExport::CREATE_ONCE;
        $file = $stored ? expPDFExportFile::facts( (string)$row['pdf_filename'] ) : array( 'exists' => false );

        $extra = array( 'modifier_name' => self::userName( (int)$export->attribute( 'modifier_id' ) ) );
        $draft = eZPDFExport::fetch( $row['id'], true, eZPDFExport::VERSION_DRAFT );
        if ( $draft instanceof eZPDFExport )
        {
            $extra['draft'] = true;
            $extra['draft_by'] = self::userName( (int)$draft->attribute( 'modifier_id' ) );
        }

        return self::infoOf( $row, $node, $file, $classNames, $extra );
    }

    /**
     * The name of the user object $userID, '' when there is none.
     *
     * @param int $userID
     * @return string
     */
    public static function userName( $userID )
    {
        static $names = array();
        if ( $userID <= 0 )
            return '';
        if ( !isset( $names[$userID] ) )
        {
            $object = eZContentObject::fetch( $userID );
            $names[$userID] = $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '';
        }
        return $names[$userID];
    }

    /**
     * The overview figures, from the cards of every export.
     *
     * @param array[] $infos
     * @param int $unfinished drafts of new exports nobody finished
     * @return array total, stored, onthefly, generated, size, missing_source, attention, unfinished
     */
    public static function summaryOf( array $infos, $unfinished = 0 )
    {
        $summary = array( 'total' => count( $infos ), 'stored' => 0, 'onthefly' => 0, 'generated' => 0, 'size' => 0,
                          'missing_source' => 0, 'attention' => 0, 'unfinished' => max( 0, (int)$unfinished ) );
        foreach ( $infos as $info )
        {
            if ( $info['status'] === 'stored' ) $summary['stored']++;
            if ( $info['status'] === 'onthefly' ) $summary['onthefly']++;
            if ( $info['file_exists'] ) { $summary['generated']++; $summary['size'] += $info['file_size']; }
            if ( $info['problem'] === 'source_missing' || $info['problem'] === 'no_source' ) $summary['missing_source']++;
            if ( $info['attention'] ) $summary['attention']++;
        }
        return $summary;
    }

    /**
     * The cards a search and a filter leave. Every word of the search has to be found in the card's search text.
     *
     * @param array[] $infos
     * @param string $search
     * @param string $filter '' or one of FILTERS
     * @return array[] keyed as given
     */
    public static function filterOf( array $infos, $search, $filter )
    {
        $words = preg_split( '/\s+/u', mb_strtolower( trim( (string)$search ) ), -1, PREG_SPLIT_NO_EMPTY );
        $out = array();
        foreach ( $infos as $key => $info )
        {
            if ( $filter === 'stored' && $info['status'] !== 'stored' ) continue;
            if ( $filter === 'onthefly' && $info['status'] !== 'onthefly' ) continue;
            if ( $filter === 'generated' && !$info['file_exists'] ) continue;
            if ( $filter === 'attention' && !$info['attention'] ) continue;
            $ok = true;
            foreach ( $words as $word )
            {
                if ( mb_strpos( $info['search'], $word ) === false ) { $ok = false; break; }
            }
            if ( $ok )
                $out[$key] = $info;
        }
        return $out;
    }

    /**
     * The order the list asked for, made into one it offers.
     *
     * @param mixed $field
     * @param mixed $direction
     * @return array field, direction ('asc' or 'desc'), opposite
     */
    public static function sortOf( $field, $direction )
    {
        $field = in_array( $field, self::SORT_FIELDS, true ) ? $field : 'title';
        if ( $direction !== 'asc' && $direction !== 'desc' )
            $direction = in_array( $field, array( 'modified', 'generated', 'size' ), true ) ? 'desc' : 'asc';
        return array( 'field' => $field, 'direction' => $direction, 'opposite' => $direction === 'asc' ? 'desc' : 'asc' );
    }

    /**
     * The cards in the order asked for; equal ones by title, then by id.
     *
     * @param array[] $infos
     * @param array $sort as sortOf() returns it
     * @return array[] keyed as given
     */
    public static function sortList( array $infos, array $sort )
    {
        $keyOf = array( 'title' => 'title', 'modified' => 'modified', 'generated' => 'file_mtime', 'size' => 'file_size', 'id' => 'id' );
        $key = $keyOf[$sort['field']];
        $sign = $sort['direction'] === 'desc' ? -1 : 1;
        uasort( $infos, function ( $a, $b ) use ( $key, $sign )
        {
            $cmp = $key === 'title' ? strnatcasecmp( $a['title'], $b['title'] ) : ( $a[$key] <=> $b[$key] );
            if ( $cmp === 0 && $key !== 'title' )
                $cmp = strnatcasecmp( $a['title'], $b['title'] ) * $sign;
            if ( $cmp === 0 )
                $cmp = ( $a['id'] <=> $b['id'] ) * $sign;
            return $cmp * $sign;
        } );
        return $infos;
    }

    /**
     * The drafts of new exports that were never finished: rows of the draft version without a stored export of
     * the same id. "New PDF export" makes one at once; Cancel removes it, leaving the page another way does not.
     *
     * @param array[] $draftRows id, modified of every draft row
     * @param int[] $publishedIDs
     * @param int $olderThan only drafts last changed before this time; 0 for all
     * @return int[] their ids
     */
    public static function unfinishedOf( array $draftRows, array $publishedIDs, $olderThan = 0 )
    {
        $published = array_flip( array_map( 'intval', $publishedIDs ) );
        $ids = array();
        foreach ( $draftRows as $row )
        {
            $id = (int)$row['id'];
            if ( isset( $published[$id] ) )
                continue;
            if ( $olderThan > 0 && (int)$row['modified'] >= $olderThan )
                continue;
            $ids[] = $id;
        }
        return $ids;
    }

    /**
     * The ids of the unfinished drafts of new exports (see unfinishedOf()).
     *
     * @param int $olderThan
     * @return int[]
     */
    public static function unfinished( $olderThan = 0 )
    {
        $drafts = eZPersistentObject::fetchObjectList( eZPDFExport::definition(), array( 'id', 'modified' ),
                                                       array( 'version' => eZPDFExport::VERSION_DRAFT ), null, null, false );
        $published = eZPersistentObject::fetchObjectList( eZPDFExport::definition(), array( 'id' ),
                                                          array( 'version' => eZPDFExport::VERSION_VALID ), null, null, false );
        $publishedIDs = array();
        foreach ( (array)$published as $row )
            $publishedIDs[] = (int)$row['id'];
        return self::unfinishedOf( (array)$drafts, $publishedIDs, $olderThan );
    }

    /**
     * Removes the drafts of new exports nobody has touched for the draft timeout (content.ini
     * [PDFExportSettings] DraftTimeout), as the edit view already does with an expired draft of a stored export.
     *
     * @param int $now
     * @return int how many were removed
     */
    public static function removeExpiredUnfinished( $now )
    {
        $timeout = (int)eZINI::instance( 'content.ini' )->variable( 'PDFExportSettings', 'DraftTimeout' );
        if ( $timeout <= 0 )
            return 0;
        $removed = 0;
        foreach ( self::unfinished( (int)$now - $timeout ) as $id )
        {
            $draft = eZPDFExport::fetch( $id, true, eZPDFExport::VERSION_DRAFT );
            if ( $draft instanceof eZPDFExport )
            {
                $draft->remove();
                $removed++;
            }
        }
        return $removed;
    }

    /**
     * Removes exports: the stored one, a draft of it, and the stored file.
     *
     * @param int[] $ids
     * @return array names (string[] the titles of those removed), files (string[] the files removed)
     */
    public static function remove( array $ids )
    {
        $names = array();
        $files = array();
        $db = eZDB::instance();
        $db->begin();
        foreach ( $ids as $id )
        {
            foreach ( array( eZPDFExport::VERSION_DRAFT, eZPDFExport::VERSION_VALID ) as $version )
            {
                $export = eZPDFExport::fetch( $id, true, $version );
                if ( !$export instanceof eZPDFExport )
                    continue;
                if ( $version === eZPDFExport::VERSION_VALID )
                {
                    $names[] = (string)$export->attribute( 'title' );
                    if ( (int)$export->attribute( 'status' ) === eZPDFExport::CREATE_ONCE
                         && expPDFExportFile::remove( (string)$export->attribute( 'pdf_filename' ) ) )
                        $files[] = (string)$export->attribute( 'pdf_filename' );
                }
                $export->remove();
            }
        }
        $db->commit();
        return array( 'names' => $names, 'files' => $files );
    }
}

?>
