<?php
// description: The folder Notes below the content root, as the run's user
// requires: 20261031130000_wp3_note_table
namespace ExpTests\Migrations\Runner;

/**
 * Publishes a folder "Notes" below the content root (node 2), owned by the run's user, and writes a note that points
 * to it. Needs the clean data (the class folder, node 2). down() removes the folder again.
 */
final class NotesFolder extends \expMigrationPhp
{
    const REMOTE_ID = 'exp-test-wp3-notes-folder';

    public function plan( $context )
    {
        return array( 'publish the folder Notes below node 2' );
    }

    public function up( $context )
    {
        // in the language of the content root, not the one the settings would pick (the installation's may differ)
        $root = \eZContentObjectTreeNode::fetch( 2 );
        if ( !$root instanceof \eZContentObjectTreeNode )
            throw new \RuntimeException( 'there is no content root (node 2)' );
        $object = \eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 2,
            'language' => $root->attribute( 'object' )->attribute( 'initial_language_code' ),
            'class_identifier' => 'folder',
            'creator_id' => \eZUser::currentUserID(),
            'remote_id' => self::REMOTE_ID,
            'attributes' => array( 'name' => 'Notes' ),
        ), false );
        if ( !$object instanceof \eZContentObject )
            throw new \RuntimeException( 'the folder Notes could not be published' );
        $context->setReference( 'notes_folder_object_id', (int)$object->attribute( 'id' ) );
        $context->db()->query( 'INSERT INTO ' . $context->reference( 'note_table', NoteTable::TABLE )
                               . ' ( id, title ) VALUES ( 2, ' . "'folder " . (int)$object->attribute( 'id' ) . "' )" );
        return array( 'published the folder Notes (object ' . (int)$object->attribute( 'id' ) . ')' );
    }

    public function down( $context )
    {
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( $object instanceof \eZContentObject )
            $object->purge();
        return array( 'removed the folder Notes' );
    }
}
