<?php
/**
 * The code of kernel/workflow/grouplist.php, moved into a class (#207 stage 1). The file kernel/workflow/grouplist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of ./kernel/workflow/grouplist.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'removeSelectedGroups' ) ) {
function removeSelectedGroups( $http, &$groups, $base )
{
    if ( $http->hasPostVariable( "DeleteGroupButton" ) )
    {
        if ( eZHTTPPersistence::splitSelected( $base,
                                               $groups, $http, "id",
                                               $keepers, $rejects ) )
        {
            $groups = $keepers;
            foreach( $rejects as $reject )
            {
                $group_id = $reject->attribute("id");

                // Remove all workflows in current group
                $list_in_group = eZWorkflowGroupLink::fetchWorkflowList( 0, $group_id, $asObject = true);
                $workflow_list = eZWorkflow::fetchList( );

                $list = array();
                foreach( $workflow_list as $workflow )
                {
                    foreach( $list_in_group as $group )
                    {
                        $id = $workflow->attribute("id");
                        $workflow_id = $group->attribute("workflow_id");
                        if ( $id === $workflow_id )
                        {
                            $list[] = $workflow;
                        }
                    }
                }
                foreach ( $list as $workFlow )
                {
                  eZTrigger::removeTriggerForWorkflow( $workFlow->attribute( 'id' ) );
                  $workFlow->remove();
                }

                $reject->remove( );
                eZWorkflowGroupLink::removeGroupMembers( $group_id );
            }
        }
    }
}
}
}

namespace Exponential\View\Kernel\Workflow
{

class Grouplist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
        {
            $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewGroupButton" ) )
        {
            $params = array();

            $Module->redirectTo( $Module->functionURI( "groupedit" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $sorting = null;

        if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
        {
            $TemplateData = array( array( "name" => "groups",
                                          "http_base" => "ContentClass",
                                          "data" => array( "command" => "group_list",
                                                           "type" => "class" ) ) );
        }

        $Module->setTitle( \ezpI18n::tr( 'kernel/workflow', 'Workflow group list' ) );
        $tpl = \eZTemplate::factory();

        $user = \eZUser::currentUser();
        foreach( $TemplateData as $tpldata )
        {
            $tplname = $tpldata["name"];
            $data = $tpldata["data"];
            $asObject = isset( $data["as_object"] ) ? $data["as_object"] : true;
            $base = $tpldata["http_base"];
            unset( $list );
            $list = \eZWorkflowGroup::fetchList( $asObject );

            $groupCount  = count( $list );
            $groupLimit  = \expAdminPagination::limit( 'workflow/grouplist' );
            $groupOffset = \expAdminPagination::offset( $Params );
            $list        = \expAdminPagination::page( $list, $groupOffset, $groupLimit );

            $tpl->setVariable( "group_count", $groupCount );
            $tpl->setVariable( "limit", $groupLimit );
            $tpl->setVariable( "view_parameters", array( 'offset' => $groupOffset ) );
            removeSelectedGroups( $http, $list, $base );
            $tpl->setVariable( $tplname, $list );
        }

        $tpl->setVariable( "module", $Module );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:workflow/grouplist.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Workflow' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Group list' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
