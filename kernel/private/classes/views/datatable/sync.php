<?php
/**
 * The datatable/sync view (doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): makes a synchronisation (/new,
 * /new/(class)/<Class>/(content_class)/<identifier> from another tool) or changes, runs and removes one. "Propose the
 * map" fills the map with the columns named like an attribute or a known source. Policy datatable/sync.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Sync extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        if ( !\expDataSync::installed() )
            return $Module->redirectTo( 'datatable/syncs' );
        $id = isset( $Params['SyncID'] ) ? (string)$Params['SyncID'] : 'new';
        $sync = $id === 'new' ? null : \expDataSyncDefinition::fetchByID( (int)$id );
        if ( $id !== 'new' && !$sync )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        if ( $isPost && $http->hasPostVariable( 'CancelButton' ) )
            return $Module->redirectTo( 'datatable/syncs' );

        $form = $sync ? array( 'name' => $sync->attribute( 'name' ), 'direction' => $sync->attribute( 'direction' ), 'mode' => $sync->attribute( 'run_mode' ),
                               'class' => $sync->attribute( 'class_name' ), 'content_class' => $sync->attribute( 'content_class' ),
                               'subtree' => (int)$sync->attribute( 'subtree_node_id' ), 'map' => \expDataSync::mapText( $sync->mapArray() ),
                               'keys' => $sync->attribute( 'key_columns' ), 'conflict' => $sync->attribute( 'conflict_rule' ),
                               'interval' => (int)$sync->attribute( 'interval_seconds' ), 'state' => $sync->attribute( 'state' ),
                               'options' => $sync->optionsArray() )
                      : array( 'name' => '', 'direction' => 'content_to_table', 'mode' => 'once', 'class' => isset( $Params['ClassName'] ) ? (string)$Params['ClassName'] : '',
                               'content_class' => isset( $Params['ContentClass'] ) ? (string)$Params['ContentClass'] : '', 'subtree' => 2, 'map' => '',
                               'keys' => '', 'conflict' => 'newest', 'interval' => 3600, 'state' => 'enabled', 'options' => array( 'remove_missing' => true ) );
        $problems = array();
        $messages = array();
        if ( $isPost && ( $http->hasPostVariable( 'SaveButton' ) || $http->hasPostVariable( 'ProposeButton' ) || $http->hasPostVariable( 'SaveRunButton' ) ) )
        {
            foreach ( array( 'name', 'direction', 'mode', 'class', 'content_class', 'subtree', 'map', 'keys', 'conflict', 'interval' ) as $field )
                if ( $http->hasPostVariable( 'Sync_' . $field ) && is_scalar( $http->postVariable( 'Sync_' . $field ) ) )
                    $form[$field] = (string)$http->postVariable( 'Sync_' . $field );
            $form['state'] = $http->hasPostVariable( 'Sync_state' ) ? 'disabled' : 'enabled';
            $form['options'] = array( 'remove_missing' => $http->hasPostVariable( 'Sync_remove_missing' ) );
            if ( $http->hasPostVariable( 'Sync_language' ) && preg_match( '/^[a-z]{3}-[A-Z]{2}$/', (string)$http->postVariable( 'Sync_language' ) ) )
                $form['options']['language'] = (string)$http->postVariable( 'Sync_language' );
            if ( $http->hasPostVariable( 'ProposeButton' ) )
            {
                $map = \expDataSync::proposeMap( $form['class'], $form['content_class'] );
                if ( $map )
                {
                    $form['map'] = \expDataSync::mapText( $map );
                    if ( trim( $form['keys'] ) === '' )
                        $form['keys'] = isset( $map['contentobject_id'] ) ? 'contentobject_id' : ( isset( $map['remote_id'] ) ? 'remote_id' : '' );
                    $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'A map was proposed: check it, then save.' ) );
                }
                else
                    $messages[] = array( 'kind' => 'warn', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'No column of the class matches an attribute or a known source: write the map yourself.' ) );
            }
            else
            {
                $saved = \expDataSync::define( $form, $problems, $sync ? (int)$sync->attribute( 'id' ) : 0 );
                if ( $saved )
                {
                    if ( $http->hasPostVariable( 'SaveRunButton' ) )
                    {
                        $run = \expDataSync::start( $saved );
                        $message = Syncs::runMessage( $saved, $run );
                        $http->setSessionVariable( 'DataTableSyncMessage', $message );
                    }
                    return $Module->redirectTo( 'datatable/sync/' . (int)$saved->attribute( 'id' ) . '/(saved)/1' );
                }
            }
        }
        if ( $isPost && $sync && $http->hasPostVariable( 'RemoveButton' ) && $http->hasPostVariable( 'ConfirmRemove' ) )
        {
            $syncID = (int)$sync->attribute( 'id' );
            \eZPersistentObject::removeObject( \expDataSyncLink::definition(), array( 'sync_id' => $syncID ) );
            \eZPersistentObject::removeObject( \expDataSyncConflict::definition(), array( 'sync_id' => $syncID ) );
            $sync->remove();
            return $Module->redirectTo( 'datatable/syncs' );
        }
        if ( isset( $Params['UserParameters']['saved'] ) )
            $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'The synchronisation was saved.' ) );
        if ( $http->hasSessionVariable( 'DataTableSyncMessage' ) )
        {
            $messages[] = (array)$http->sessionVariable( 'DataTableSyncMessage' );
            $http->removeSessionVariable( 'DataTableSyncMessage' );
        }

        $classes = array();
        foreach ( \expDataTableCatalogue::instance()->classes() as $name => $entry )
            if ( $entry['error'] === '' && $entry['table'] !== '' && ( $entry['origin'] !== 'kernel' || $entry['connection'] !== '' ) )
                $classes[] = array( 'class' => $name, 'origin' => $entry['origin'], 'connection' => $entry['connection'] );
        $contentClasses = array();
        foreach ( (array)\eZContentClass::fetchList( \eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
            $contentClasses[] = array( 'identifier' => (string)$class->attribute( 'identifier' ), 'name' => (string)$class->attribute( 'name' ) );
        usort( $contentClasses, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        $node = $form['subtree'] ? \eZContentObjectTreeNode::fetch( (int)$form['subtree'] ) : null;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'sync', $sync );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'problems', $problems );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'classes', $classes );
        $tpl->setVariable( 'content_classes', $contentClasses );
        $tpl->setVariable( 'node_name', $node ? (string)$node->attribute( 'name' ) : '' );
        $tpl->setVariable( 'meta_sources', \expDataSync::META );
        $runs = array();
        if ( $sync && class_exists( '\expKernelTransferRunSchema' ) && \expKernelTransferRunSchema::exists() )
        {
            $list = \eZPersistentObject::fetchObjectList( \expKernelTransferRun::definition(), null,
                                                          array( 'tool' => \expDataSync::TOOL, 'tool_ref' => (string)$sync->attribute( 'id' ) ),
                                                          array( 'started' => 'desc', 'id' => 'desc' ), array( 'offset' => 0, 'length' => 10 ) );
            $runs = is_array( $list ) ? $list : array();
        }
        $tpl->setVariable( 'runs', $runs );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/sync.tpl' );
        $Result['path'] = array( array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => 'datatable/syncs', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Synchronisations' ) ),
                                 array( 'url' => false, 'text' => $sync ? (string)$sync->attribute( 'name' ) : \ezpI18n::tr( 'design/admin/datatable', 'New synchronisation' ) ) );
        return $Result;
    }
}

}
?>
