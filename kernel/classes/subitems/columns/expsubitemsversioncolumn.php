<?php
/**
 * Subitems list columns about the versions of the content object.
 *
 * Field= picks the column: version, version_count, draft_count, draft_authors, latest_draft,
 * archived_count, pending_count, rejected_count, initial_creator, contributors, version_created,
 * workflow_processes. Every version field of a row shares one query: the object's version rows
 * (ezcontentobject_version, no data), fetched once per object per request.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsVersionColumn extends expSubitemsFieldColumn
{
    /** The current (published) version number. */
    protected function fieldVersion( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        return $object ? (int)$object->attribute( 'current_version' ) : null;
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
        return $this->countStatus( $node, array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ) );
    }

    /** The names of the users who have an open draft. */
    protected function fieldDraftAuthors( eZContentObjectTreeNode $node )
    {
        $rows = $this->versionRows( $node );
        if ( $rows === null )
            return null;
        $names = array();
        foreach ( $rows as $row )
        {
            if ( in_array( (int)$row['status'], array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true ) )
            {
                $name = expSubitemsObjectColumn::objectName( (int)$row['creator_id'] );
                if ( $name !== null )
                    $names[$name] = $name;
            }
        }
        return array_values( $names );
    }

    /** When the most recent open draft was last changed; null when there is none. */
    protected function fieldLatestDraft( eZContentObjectTreeNode $node )
    {
        $rows = $this->versionRows( $node );
        if ( $rows === null )
            return null;
        $latest = 0;
        foreach ( $rows as $row )
        {
            if ( in_array( (int)$row['status'], array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true ) )
                $latest = max( $latest, (int)$row['modified'] );
        }
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
        $names = array();
        foreach ( $rows as $row )
        {
            $name = expSubitemsObjectColumn::objectName( (int)$row['creator_id'] );
            if ( $name !== null )
                $names[$name] = $name;
        }
        return array_values( $names );
    }

    /** When the current version was created (it was published later, see the Modified column). */
    protected function fieldVersionCreated( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        $rows = $this->versionRows( $node );
        if ( !$object || $rows === null )
            return null;
        $current = (int)$object->attribute( 'current_version' );
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
        $object = self::object( $node );
        if ( !$object )
            return null;
        return (int)eZPersistentObject::count( eZWorkflowProcess::definition(),
                                               array( 'content_id' => (int)$object->attribute( 'id' ) ) );
    }

    /** The version rows (no attribute data) of the node's object, one query per object per request. */
    protected function versionRows( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $id = (int)$object->attribute( 'id' );
        return self::memo( 'versions', $id, function () use ( $object )
        {
            $rows = $object->versions( false );
            return is_array( $rows ) ? $rows : array();
        } );
    }

    protected function countStatus( eZContentObjectTreeNode $node, array $statuses )
    {
        $rows = $this->versionRows( $node );
        if ( $rows === null )
            return null;
        $n = 0;
        foreach ( $rows as $row )
        {
            if ( in_array( (int)$row['status'], $statuses, true ) )
                $n++;
        }
        return $n;
    }
}
