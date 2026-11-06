<?php
/**
 * The imports of URL maps (content/urlimporthistory), policy content/urltranslator_import:
 *
 *   /content/urlimporthistory[/(offset)/<n>]                 every import, newest first: who, when, from which file,
 *                                                            the counts and the state
 *   /content/urlimporthistory/<batch id>[/(offset)/<n>]      one import: its options, the entries it wrote, a page at
 *                                                            a time, and Undo
 *
 * Undo (UndoButton) asks "Verify it's you" (site.ini [ReauthGroup_system]) and is expUrlMapImporter::undo(): every
 * entry back as it was, the last first. Entries changed after the import are listed above the button and kept unless
 * "Also undo the changed entries" (the console's --force) is ticked.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @since 6.0.15
 */

namespace Exponential\View\Kernel\Content
{

class UrlImportHistory extends \Exponential\Runnable\ModuleView
{
    const PAGE_SIZE = 25;
    const ITEM_PAGE_SIZE = 50;

    /**
     * A batch for the templates (the user's name added). Pure apart from the user's name.
     *
     * @param \expUrlMapBatch $batch
     * @return array
     */
    public static function batchRow( \expUrlMapBatch $batch )
    {
        $row = $batch->asArray();
        $row['user_name'] = self::userName( $row['user_id'] );
        $row['undone_by_name'] = $row['undone_by'] ? self::userName( $row['undone_by'] ) : '';
        $row['undoable'] = $row['state'] !== \expUrlMapBatch::STATE_UNDONE && ( $row['created_count'] + $row['updated_count'] ) > 0;
        return $row;
    }

    /**
     * Where an entry an import wrote leads, in short: an alias's action, a wildcard's destination, a redirect's
     * target with its status. Pure.
     *
     * @param string $table
     * @param array|null $row
     * @return string
     */
    public static function targetText( $table, $row )
    {
        if ( !is_array( $row ) )
            return '';
        switch ( $table )
        {
            case 'ezurlalias_ml':
                return (string)( isset( $row['action'] ) ? $row['action'] : '' ) . ( !empty( $row['alias_redirects'] ) ? ' (301)' : '' );
            case 'ezurlwildcard':
                return (string)( isset( $row['destination_url'] ) ? $row['destination_url'] : '' )
                       . ( isset( $row['type'] ) && \eZURLWildcard::isForwardType( (int)$row['type'] ) ? ' (' . \eZURLWildcard::redirectStatusOfType( (int)$row['type'] ) . ')' : '' );
            case \expUrlRedirect::TABLE:
                return (string)( isset( $row['target'] ) ? $row['target'] : '' ) . ( isset( $row['status'] ) ? ' (' . (int)$row['status'] . ')' : '' );
        }
        return '';
    }

    /**
     * @param int $userId
     * @return string
     */
    protected static function userName( $userId )
    {
        if ( !$userId )
            return '';
        $object = \eZContentObject::fetch( (int)$userId );
        return $object ? (string)$object->attribute( 'name' ) : '#' . (int)$userId;
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];

        $Module = $Params['Module'];
        $offset = isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $batchId = isset( $Params['BatchID'] ) && is_numeric( $Params['BatchID'] ) ? (int)$Params['BatchID'] : 0;
        $tables = \expUrlMapBatch::tablesExist();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'import_tables', $tables );
        $title = \ezpI18n::tr( 'design/admin/content/urlimport', 'Import history' );

        if ( $batchId > 0 )
        {
            $batch = $tables ? \expUrlMapBatch::fetch( $batchId ) : null;
            if ( !$batch )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
            $importer = new \expUrlMapImporter();
            $notice = '';
            $error = '';
            $http = \eZHTTPTool::instance();
            if ( \expViewParameters::isPost() && $http->hasPostVariable( 'UndoButton' ) )
            {
                $result = null;
                try
                {
                    $result = $importer->undo( $batchId, array( 'force' => $http->hasPostVariable( 'UndoForce' ), 'user_id' => (int)\eZUser::currentUserID() ) );
                }
                catch ( \RuntimeException $e )
                {
                    $error = $e->getMessage();
                }
                if ( $result !== null )
                    \expViewParameters::redirect( 'content/urlimporthistory/' . $batchId . '/(done)/' . ( $result['counts']['kept'] ? 'partly' : 'undone' ) );
            }
            $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
            if ( isset( $userParameters['done'] ) && in_array( $userParameters['done'], array( 'undone', 'partly' ), true ) )
                $notice = (string)$userParameters['done'];

            $row = self::batchRow( $batch );
            $count = $batch->itemCount();
            if ( $offset >= $count )
                $offset = 0;
            $items = array_map( function ( \expUrlMapBatchItem $item ) {
                $row = $item->asArray();
                $row['target'] = self::targetText( $row['target_table'], $row['after_image'] !== null ? $row['after_image'] : $row['before_image'] );
                unset( $row['before_image'], $row['after_image'] );
                return $row;
            }, $batch->itemsPage( $offset, self::ITEM_PAGE_SIZE ) );
            $tpl->setVariable( 'batch', $row );
            $tpl->setVariable( 'batch_items', $items );
            $tpl->setVariable( 'batch_item_count', $count );
            $tpl->setVariable( 'batch_item_limit', self::ITEM_PAGE_SIZE );
            $tpl->setVariable( 'batch_changed', $row['undoable'] || $row['state'] === \expUrlMapBatch::STATE_PARTLY_UNDONE
                                                ? $importer->changedSince( $batchId, $row['state'] === \expUrlMapBatch::STATE_PARTLY_UNDONE ) : array() );
            $tpl->setVariable( 'batch_notice', $notice );
            $tpl->setVariable( 'batch_error', $error );
            $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
            $name = \ezpI18n::tr( 'design/admin/content/urlimport', 'Import %id', null, array( '%id' => $batchId ) );
            $Module->setTitle( $name );
            return $this->viewResult( array( 'content' => $tpl->fetch( 'design:content/urlimport_batch.tpl' ),
                                             'path' => array( array( 'url' => '/content/urltranslator', 'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) ),
                                                              array( 'url' => '/content/urlimporthistory', 'text' => $title ),
                                                              array( 'url' => false, 'text' => $name ) ) ), null );
        }

        $count = $tables ? \expUrlMapBatch::fetchListCount() : 0;
        if ( $offset >= $count )
            $offset = 0;
        $batches = $tables ? array_map( array( __CLASS__, 'batchRow' ), \expUrlMapBatch::fetchList( $offset, self::PAGE_SIZE ) ) : array();
        $tpl->setVariable( 'batches', $batches );
        $tpl->setVariable( 'batch_count', $count );
        $tpl->setVariable( 'batch_limit', self::PAGE_SIZE );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $Module->setTitle( $title );
        return $this->viewResult( array( 'content' => $tpl->fetch( 'design:content/urlimport_history.tpl' ),
                                         'path' => array( array( 'url' => '/content/urltranslator', 'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) ),
                                                          array( 'url' => false, 'text' => $title ) ) ), null );
    }
}

}
