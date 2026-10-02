<?php
/**
 * Subitems list columns that tell what the current user may do with each item, and technical
 * facts about it.
 *
 * Field= picks the column: can_edit, can_remove, can_move, can_hide, can_translate, can_create,
 * search_words. The can_* fields are the kernel's own checks (the same as the edit, remove,
 * move and hide buttons), answered from the user's cached policies, so they cost no query for
 * most limitations. search_words counts the rows the built-in search engine (eZSearchEngine)
 * indexed for the object, one grouped count query for the page; with another search engine it is null.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsPermissionColumn extends expSubitemsFieldColumn
{
    protected function fieldCanEdit( eZContentObjectTreeNode $node )
    {
        return (bool)$node->canEdit();
    }

    protected function fieldCanRemove( eZContentObjectTreeNode $node )
    {
        return (bool)$node->canRemove();
    }

    protected function fieldCanMove( eZContentObjectTreeNode $node )
    {
        return (bool)$node->canMoveFrom();
    }

    protected function fieldCanHide( eZContentObjectTreeNode $node )
    {
        return (bool)$node->canHide();
    }

    protected function fieldCanTranslate( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        return $object ? (bool)$object->canTranslate() : null;
    }

    /** May create content below the item; null when its class is not a container. */
    protected function fieldCanCreate( eZContentObjectTreeNode $node )
    {
        if ( !$node->attribute( 'is_container' ) )
            return null;
        return (bool)$node->canCreate();
    }

    /** Word positions the built-in search engine indexed for the object (ezsearch_object_word_link). */
    protected function fieldSearchWords( eZContentObjectTreeNode $node )
    {
        $objectID = (int)$node->attribute( 'contentobject_id' );
        if ( !self::usesBuiltinSearch() || !self::object( $node ) )
            return null;
        return self::memo( 'searchwords', $objectID, function () use ( $objectID )
        {
            return (int)eZPersistentObject::count( expSubitemsSearchWordLinkRow::definition(), array( 'contentobject_id' => $objectID ) );
        } );
    }

    protected static function prefetchSets()
    {
        return array( 'SearchWords' => array( 'search_words' ) );
    }

    /** The indexed word counts of the page's objects, one grouped query. */
    protected function prefetchSearchWords( array $nodes )
    {
        if ( self::usesBuiltinSearch() )
            self::prefetchCounts( 'searchwords', expSubitemsSearchWordLinkRow::definition(), 'contentobject_id', self::objectIDs( $nodes ) );
    }

    /** Whether [SearchSettings] SearchEngine is the built-in eZSearchEngine. */
    protected static function usesBuiltinSearch()
    {
        $engine = (string)eZINI::instance()->variable( 'SearchSettings', 'SearchEngine' );
        return strcasecmp( $engine, 'eZSearchEngine' ) === 0 || strcasecmp( $engine, 'ezsearch' ) === 0;
    }
}
