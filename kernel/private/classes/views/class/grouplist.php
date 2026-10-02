<?php
/**
 * The code of kernel/class/grouplist.php, moved into a class (#207 stage 1). The file kernel/class/grouplist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/grouplist.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
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
        if ( $http->hasPostVariable( "RemoveGroupButton" ) )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
                if ( $deleteIDArray !== null )
                {
                    $http->setSessionVariable( 'DeleteGroupIDArray', $deleteIDArray );
                    $Module->redirectTo( $Module->functionURI( 'removegroup' ) . '/' );
                }
            }
        }

        if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
        {
            $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewGroupButton" ) )
        {
            $params = array();
            $Module->run( "groupedit", $params );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewClassButton" ) )
        {
            if ( $http->hasPostVariable( "SelectedGroupID" ) )
            {
                $groupID = $http->postVariable( "SelectedGroupID" );
                $group = \eZContentClassGroup::fetch( $groupID );
                $groupName = $group->attribute( 'name' );

                $params = array( null, $groupID, $groupName );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( "edit", $params ) );
            }
        }

        if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
        {
            $TemplateData = array( array( "name" => "groups",
                                          "http_base" => "ContentClass",
                                          "data" => array( "command" => "group_list",
                                                           "type" => "class" ) ) );
        }

        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Class group list' ) );
        $tpl = \eZTemplate::factory();

        $user = \eZUser::currentUser();
        foreach( $TemplateData as $tpldata )
        {
            $tplname = $tpldata["name"];
            $data = $tpldata["data"];
            $asObject = isset( $data["as_object"] ) ? $data["as_object"] : true;
            $base = $tpldata["http_base"];
            unset( $list );
            $list = \eZContentClassGroup::fetchList( false, $asObject );

            $groupCount  = count( $list );
            $groupLimit  = \expAdminPagination::limit( 'class/grouplist' );
            $groupOffset = \expAdminPagination::offset( $Params );

            $tpl->setVariable( $tplname, \expAdminPagination::page( $list, $groupOffset, $groupLimit ) );
            $tpl->setVariable( "group_count", $groupCount );
            $tpl->setVariable( "limit", $groupLimit );
            $tpl->setVariable( "view_parameters", array( 'offset' => $groupOffset ) );
        }

        $tpl->setVariable( "module", $Module );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:class/grouplist.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
