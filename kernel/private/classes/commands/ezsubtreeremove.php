<?php
/**
 * The code of bin/php/ezsubtreeremove.php, moved into a class (#207 stage 1). The file bin/php/ezsubtreeremove.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezsubtreeremove.php:
 *
 *
 * File containing the ezsubtreeremove.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Remove content subtrees by node ID, to the trash unless --ignore-trash is given (dry run unless --apply)
 * @long-description Permanently removes all content objects under the specified subtree nodes. This operation is irreversible. Use --dry-run first to preview what will be deleted.
 *
 */

namespace Exponential\Command\Kernel
{

class Ezsubtreeremove extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'canRemove', 'canRemoveAll', 'childCount', 'cli', 'deleteIDArray', 'deleteIDArrayResult', 'deleteItem', 'deleteResult', 'info', 'ini', 'itemDeleteList', 'itemInfo', 'itemTotalChildCount', 'moveToTrash', 'moveToTrashStr', 'node', 'nodeID', 'nodeName', 'objectNodeCount', 'reverseRelatedCount', 'script', 'scriptOptions', 'srcNodesID', 'totalChildCount', 'user', 'userCreatorID' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "\n" .
                                                                 "This script will make a remove of a content object subtrees.\n"
                                                                 . "Without --apply nothing is removed: the script lists the subtrees, their child and object\n"
                                                                 . "counts and whether they may be removed. --dry-run means the same as no flag; --apply together\n"
                                                                 . "with --dry-run is a usage error (exit 2).\n\n"
                                                                 . "Example: ./bin/php/ezsubtreeremove.php --nodes-id=70,71 --ignore-trash\n"
                                                                 . "         ./bin/php/ezsubtreeremove.php --nodes-id=70,71 --ignore-trash --apply\n" ),
                                              'use-session' => false,
                                              'use-modules' => true,
                                              'use-extensions' => true ) );
        $scriptOptions = $this->startup( "[apply][dry-run][nodes-id:][ignore-trash]",
                                              "",
                                              array( 'apply' => "Remove the subtrees (the default is a dry run that lists them and removes nothing)",
                                                     'dry-run' => "The default: list what would be removed, change nothing",
                                                     'nodes-id' => "Subtree nodes ID (separated by comma ',').",
                                                     'ignore-trash' => "Ignore trash ('move to trash' by default)."
                                                     ),
                                              false );
        $apply = self::applyRequested( $scriptOptions );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $srcNodesID  = $scriptOptions[ 'nodes-id' ] ? trim( $scriptOptions[ 'nodes-id' ] ) : false;
        $moveToTrash = $scriptOptions[ 'ignore-trash' ] ? false : true;
        $deleteIDArray = $srcNodesID ? explode( ',', $srcNodesID ) : false;

        if ( !$deleteIDArray )
        {
            $cli->error( "Subtree remove Error!\nCannot get subtree nodes. Please check nodes-id argument and try again." );
            $script->showHelp();
            $script->shutdown( 1 );
        }

        $ini = \eZINI::instance();
        // Get user's ID who can remove subtrees. (Admin by default with userID = 14)
        $userCreatorID = $ini->variable( "UserSettings", "UserCreatorID" );
        $user = \eZUser::fetch( $userCreatorID );
        if ( !$user )
        {
            $cli->error( "Subtree remove Error!\nCannot get user object by userID = '$userCreatorID'.\n(See site.ini[UserSettings].UserCreatorID)" );
            $script->shutdown( 1 );
        }
        \eZUser::setCurrentlyLoggedInUser( $user, $userCreatorID );

        $deleteIDArrayResult = array();
        foreach ( $deleteIDArray as $nodeID )
        {
            $node = \eZContentObjectTreeNode::fetch( $nodeID );
            if ( $node === null )
            {
                $cli->error( "\nSubtree remove Error!\nCannot find subtree with nodeID: '$nodeID'." );
                continue;
            }
            $deleteIDArrayResult[] = $nodeID;
        }
        // Get subtree removal information
        $info = \eZContentObjectTreeNode::subtreeRemovalInformation( $deleteIDArrayResult );

        $deleteResult = $info['delete_list'];

        if ( count( $deleteResult ) == 0 )
        {
            $cli->output( "\nExit." );
            $script->shutdown( 1 );
        }

        $totalChildCount = $info['total_child_count'];
        $canRemoveAll = $info['can_remove_all'];
        $moveToTrashStr = $moveToTrash ? 'true' : 'false';
        $reverseRelatedCount = $info['reverse_related_count'];

        $cli->output( "\nTotal child count: $totalChildCount" );
        $cli->output( "Move to trash: $moveToTrashStr" );
        $cli->output( "Reverse related count: $reverseRelatedCount\n" );

        $cli->output( $dryRun ? "Subtrees that would be removed:\n" : "Removing subtrees:\n" );
        $planned = 0;
        $failed = 0;

        foreach ( $deleteResult as $deleteItem )
        {
            $node = $deleteItem['node'];
            $nodeName = $deleteItem['node_name'];
            if ( $node === null )
            {
                $cli->error( "\nSubtree remove Error!\nCannot find subtree '$nodeName'." );
                continue;
            }
            $nodeID = $node->attribute( 'node_id' );
            $childCount = $deleteItem['child_count'];
            $objectNodeCount = $deleteItem['object_node_count'];

            $cli->output( "Node id: $nodeID" );
            $cli->output( "Node name: $nodeName" );

            $canRemove = $deleteItem['can_remove'];
            if ( !$canRemove )
            {
                $cli->error( "\nSubtree remove Error!\nInsufficient permissions. You do not have permissions to remove the subtree with nodeID: $nodeID\n" );
                $failed++;
                continue;
            }
            $cli->output( "Child count: $childCount" );
            $cli->output( "Object node count: $objectNodeCount" );
            $planned++;
            if ( $dryRun )
            {
                $cli->output( "would be removed" . ( $moveToTrash ? ' (moved to the trash)' : '' ) . "\n" );
                continue;
            }

            // Remove subtrees
            if ( !$dryRun )
                \eZContentObjectTreeNode::removeSubtrees( array( $nodeID ), $moveToTrash );

            // We should make sure that all subitems have been removed.
            $itemInfo = \eZContentObjectTreeNode::subtreeRemovalInformation( array( $nodeID ) );
            $itemTotalChildCount = $itemInfo['total_child_count'];
            $itemDeleteList = $itemInfo['delete_list'];

            if ( count( $itemDeleteList ) != 0 or ( $childCount != 0 and $itemTotalChildCount != 0 ) )
            {
                $cli->error( "\nWARNING!\nSome subitems have not been removed.\n" );
                $failed++;
            }
            else
                $cli->output( "Successfuly DONE.\n" );
        }

        if ( $dryRun )
        {
            $cli->output( "PASS: $planned subtree(s) would be removed (dry run, nothing changed; --apply writes)" );
            $script->shutdown( $failed ? 1 : 0 );
            return;
        }
        if ( $failed )
        {
            $cli->error( "FAIL: $failed subtree(s) not removed or not completely" );
            $script->shutdown( 1 );
        }
        $cli->output( "PASS: $planned subtree(s) removed" );
        $script->shutdown();
    }
}

}
