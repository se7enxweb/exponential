<?php
/**
 * Subitems list columns about the versions of the content object.
 *
 * Field= picks the column: version, version_count, draft_count, draft_authors, latest_draft,
 * archived_count, pending_count, rejected_count, initial_creator, contributors, version_created,
 * workflow_processes. Every version field of a row shares one query: the object's version rows
 * (ezcontentobject_version, no data), fetched once per object per request, or for the whole page
 * at once (prefetch()); workflow_processes is one grouped count for the page.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsVersionColumn extends expSubitemsFieldColumn
{
    /** The statuses of a version that is not published yet: ordinary and internal drafts. */
    const DRAFT_STATUSES = array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT );

    /** The current (published) version number. */
    protected function fieldVersion( eZContentObjectTreeNode $node )
    {
        return self::objectInt( $node, 'current_version' );
    }

    /** Every stored version, whatever its status. */
    protected function fieldVersionCount( eZContentObjectTreeNode $node )
    {
        $rows = $this->versionRows( $node );
        return $rows === null ? null : count( $rows );
    }

    /** Drafts (ordinary and internal) that are not published yet. */
    protected function fieldDraftCount( eZContentObjectTreeNode $node )
    {
        return $this->countStatus( $node, self::DRAFT_STATUSES );
    }

    /** The names of the users who have an open draft. */
    protected function fieldDraftAuthors( eZContentObjectTreeNode $node )
    {
        $rows = $this->rowsWithStatus( $node, self::DRAFT_STATUSES );
        return $rows === null ? null : self::creatorNames( $rows );
    }

    /** When the most recent open draft was last changed; null when there is none. */
    protected function fieldLatestDraft( eZContentObjectTreeNode $node )
    {
        $rows = $this->rowsWithStatus( $node, self::DRAFT_STATUSES );
        if ( $rows === null )
            return null;
        $latest = 0;
        foreach ( $rows as $row )
            $latest = max( $latest, (int)$row['modified'] );
        return $latest > 0 ? $latest : null;
    }

    protected function fieldArchivedCount( eZContentObjectTreeNode $node )
    {
        return $this->countStatus( $node, array( eZContentObjectVersion::STATUS_ARCHIVED ) );
    }

    /** Versions waiting in a workflow (an approval, for instance). */
    protected function fieldPendingCount( eZContentObjectTreeNode $node )
    {
        return $this->countStatus( $node, array( eZContentObjectVersion::STATUS_PENDING, eZContentObjectVersion::STATUS_QUEUED ) );
    }

    protected function fieldRejectedCount( eZContentObjectTreeNode $node )
    {
        return $this->countStatus( $node, array( eZContentObjectVersion::STATUS_REJECTED ) );
    }

    /** Who wrote version 1 (or the lowest version kept). */
    protected function fieldInitialCreator( eZContentObjectTreeNode $node )
    {
        $rows = $this->versionRows( $node );
        if ( !$rows )
            return null;
        $first = null;
        foreach ( $rows as $row )
        {
            if ( $first === null || (int)$row['version'] < (int)$first['version'] )
                $first = $row;
        }
        return expSubitemsObjectColumn::objectName( (int)$first['creator_id'] );
    }

    /** Everyone who wrote a version, in the order of their first version. */
    protected function fieldContributors( eZContentObjectTreeNode $node )
    {
        $rows = $this->versionRows( $node );
        if ( $rows === null )
            return null;
        usort( $rows, function ( $a, $b ) { return (int)$a['version'] - (int)$b['version']; } );
        return self::creatorNames( $rows );
    }

    /** When the current version was created (it was published later, see the Modified column). */
    protected function fieldVersionCreated( eZContentObjectTreeNode $node )
    {
        $current = self::objectInt( $node, 'current_version' );
        $rows = $this->versionRows( $node );
        if ( $current === null || $rows === null )
            return null;
        foreach ( $rows as $row )
        {
            if ( (int)$row['version'] === $current )
                return (int)$row['created'] > 0 ? (int)$row['created'] : null;
        }
        return null;
    }

    /** Workflow processes running for the object (ezworkflow_process), e.g. an approval or a delayed publish. */
    protected function fieldWorkflowProcesses( eZContentObjectTreeNode $node )
    {
        $objectID = self::objectInt( $node, 'id' );
        if ( $objectID === null )
            return null;
        return self::memo( 'workflowprocesses', $objectID, function () use ( $objectID )
        {
            return (int)eZPersistentObject::count( eZWorkflowProcess::definition(), array( 'content_id' => $objectID ) );
        } );
    }

    protected static function prefetchSets()
    {
        return array( 'Versions' => array( 'version_count', 'draft_count', 'draft_authors', 'latest_draft', 'archived_count',
                                           'pending_count', 'rejected_count', 'initial_creator', 'contributors', 'version_created' ),
                      'WorkflowProcesses' => array( 'workflow_processes' ) );
    }

    /** The version rows of every object of the page, one query (the rows versions( false ) gives per object). */
    protected function prefetchVersions( array $nodes )
    {
        $ids = self::notMemoised( 'versions', self::objectIDs( $nodes ) );
        if ( !$ids )
            return;
        $rows = eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(), null,
                                                     array( 'contentobject_id' => array( $ids ) ), null, null, false );
        if ( !is_array( $rows ) )
            return;
        $byObject = array_fill_keys( $ids, array() );
        foreach ( $rows as $row )
            $byObject[(int)$row['contentobject_id']][] = $row;
        foreach ( $byObject as $id => $objectRows )
            self::remember( 'versions', $id, $objectRows );
    }

    /** The running workflow processes of the page's objects, one grouped query. */
    protected function prefetchWorkflowProcesses( array $nodes )
    {
        self::prefetchCounts( 'workflowprocesses', eZWorkflowProcess::definition(), 'content_id', self::objectIDs( $nodes ) );
    }

    /** The version rows (no attribute data) of the node's object, one query per object per request. */
    protected function versionRows( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        return self::memo( 'versions', (int)$object->attribute( 'id' ), function () use ( $object )
        {
            $rows = $object->versions( false );
            return is_array( $rows ) ? $rows : array();
        } );
    }

    /** The version rows with one of $statuses, null when there is no object. */
    protected function rowsWithStatus( eZContentObjectTreeNode $node, array $statuses )
    {
        $rows = $this->versionRows( $node );
        if ( $rows === null )
            return null;
        return array_values( array_filter( $rows, function ( $row ) use ( $statuses )
        {
            return in_array( (int)$row['status'], $statuses, true );
        } ) );
    }

    protected function countStatus( eZContentObjectTreeNode $node, array $statuses )
    {
        $rows = $this->rowsWithStatus( $node, $statuses );
        return $rows === null ? null : count( $rows );
    }

    /** The names of the creators of version rows, each once, in the rows' order. */
    protected static function creatorNames( array $rows )
    {
        $names = array();
        foreach ( $rows as $row )
        {
            $name = expSubitemsObjectColumn::objectName( (int)$row['creator_id'] );
            if ( $name !== null )
                $names[$name] = $name;
        }
        return array_values( $names );
    }
}
