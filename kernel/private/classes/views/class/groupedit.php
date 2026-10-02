<?php
/**
 * The code of kernel/class/groupedit.php, moved into a class (#207 stage 1). The file kernel/class/groupedit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/groupedit.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
{

class Groupedit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $GroupID = null;
        if ( isset( $Params["GroupID"] ) )
            $GroupID = $Params["GroupID"];

        if ( is_numeric( $GroupID ) )
        {
            $classgroup = \eZContentClassGroup::fetch( $GroupID );
        }
        else
        {
            $user = \eZUser::currentUser();
            $user_id = $user->attribute( "contentobject_id" );
            $classgroup = \eZContentClassGroup::create( $user_id );
            $classgroup->setAttribute( "name", \ezpI18n::tr( 'kernel/class/groupedit', "New Group" ) );
            $classgroup->store();
            $GroupID = $classgroup->attribute( "id" );
            $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $GroupID );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( "DiscardButton" ) )
        {
            $Module->redirectTo( $Module->functionURI( "grouplist" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "StoreButton" ) )
        {
            if ( $http->hasPostVariable( "Group_name" ) )
            {
                $name = $http->postVariable( "Group_name" );
            }
            $classgroup->setAttribute( "name", $name );
            // Set new modification date
            $date_time = time();
            $classgroup->setAttribute( "modified", $date_time );
            $user = \eZUser::currentUser();
            $user_id = $user->attribute( "contentobject_id" );
            $classgroup->setAttribute( "modifier_id", $user_id );
            $classgroup->store();

            \eZContentClassClassGroup::update( null, $GroupID, $name );

            \ezpEvent::getInstance()->notify( 'content/class/group/cache', array( $classgroup->attribute( 'id' ) ) );
            $Module->redirectToView( 'classlist', array( $classgroup->attribute( 'id' ) ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $Module->setTitle( "Edit class group " . $classgroup->attribute( "name" ) );

        // Template handling
        $tpl = \eZTemplate::factory();

        $res = \eZTemplateDesignResource::instance();
        $res->setKeys( array( array( "classgroup", $classgroup->attribute( "id" ) ) ) );

        $tpl->setVariable( "http", $http );
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "classgroup", $classgroup );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:class/groupedit.tpl" );
        $Result['path'] = array( array( 'url' => '/class/grouplist/',
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                 array( 'url' => false,
                                        'text' => $classgroup->attribute( 'name' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
